<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\DistributionSmsInfo;

/**
 * Class: Distribution
 * @author A.Kravchuk <taptak1989@gmail.com>
 */
class Distribution
{

    /**
     * em
     *
     * @var mixed
     */
    private $em;

    /**
     * container
     *
     * @var mixed
     */
    private $container;

    /**
     * __construct
     *
     * @param EntityManager $em
     * @param ContainerInterface $container
     */
    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function sendDistribution()
    {
        $distributions = $this->em->getRepository('AppBundle:EmailAndSmsDistribution')->findBy(['active' => true]);
        $uniSender = $this->em->getRepository('AppBundle:Unisender')->findOneBy(['active' => '1']);
        foreach ($distributions as $distribution) {
            $users = $distribution->getUsers();
            if ($distribution->getSendSms() && $uniSender) {
                foreach ($users as $user) {
                    $result = $this->sendSmsRequest($uniSender, $user->getPhone(), $distribution->getSmsText());
                    $DistributionSmsInfo = new DistributionSmsInfo();
                    $DistributionSmsInfo->setDistribution($distribution);
                    $DistributionSmsInfo->setSmsId($result['sms_id']);
                    $DistributionSmsInfo->setSmsStatus('Сообщение пока не отправлено, ждёт отправки. Статус будет изменён после отправки');
                    $DistributionSmsInfo->setUsers($user);
                    $this->em->persist($DistributionSmsInfo);
                    $this->em->flush($DistributionSmsInfo);
                }
            }

            if ($distribution->getSendEmail()) {
                foreach ($users as $user) {
                    $message = \Swift_Message::newInstance()
                        ->setSubject($distribution->getEmailTitle())
                        ->setFrom('mo-reply@sirius-sport.com')
                        ->addTo($user->getEmail())
                        ->setBody($distribution->getEmailText())
                        ->setContentType("text/html");
                    $this->container->get('mailer')->send($message);
                }
            }

        }
    }

    /**
     * sendSmsRequest
     *
     * @param Unisender $uniSender
     * @param mixed $phone
     * @param string $sms_text
     *
     * return mixed
     */
    public function sendSmsRequest($uniSender, $phone, $sms_text)
    {
        if ($curl = curl_init()) {
            // массив передаваемых параметров
            $parameters_array = array(
                'api_key' => $uniSender->getApiKey(),
                'phone' => preg_replace("/[^0-9]/", '', strip_tags($phone)),
                'sender' => $uniSender->getSenderName(),
                'text' => $sms_text
            );

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $parameters_array);
            curl_setopt($curl, CURLOPT_TIMEOUT, 10);
            curl_setopt($curl, CURLOPT_URL, 'http://api.unisender.com/ru/api/sendSms?format=json');
            $response = curl_exec($curl);

            if ($response) {
                // Раскодируем ответ API-сервера
                $jsonObj = json_decode($response);
                if (isset($jsonObj->error)) {
                    // Ошибка в полученном ответе
                    $result['error'] = "An error occured: {$jsonObj->error} (code: {$jsonObj->code})";
                } elseif (isset($jsonObj->result) && isset($jsonObj->result->error)) {
                    // Ошибка отправки сообщения
                    $result['error'] = "An error occured: {$jsonObj->result->error} (code: {$jsonObj->result->code})";
                } else {
                    // Сообщение успешно отправлено
                    $result['sms_id'] = $jsonObj->result->sms_id;
                    $result['error'] = false;
                }
            } else {
                // Ошибка соединения с API-сервером
                $result['error'] = "API access error";
            }
            curl_close($curl);

            return $result;
        }
    }

    /**
     * @return int count of updated sms statuses
     */
    public function checkSmsStatus()
    {
        $DistributionSmsInfos = $this->em
            ->createQuery('SELECT s FROM AppBundle\Entity\SmsInfo s WHERE s.smsId != :where AND s.smsStatus NOT LIKE :like')
            ->setParameter('where', 'null')
            ->setParameter('like', '%Статус окончательный%')
            ->getResult();
        $count = 0;
        $status_arr = [
            'not_sent' => 'Сообщение пока не отправлено, ждёт отправки. Статус будет изменён после отправки',
            'ok_sent' => 'Сообщение отправлено, но статус доставки пока неизвестен. Статус временный и может измениться',
            'ok_delivered' => 'Сообщение доставлено. Статус окончательный',
            'err_src_invalid' => 'Доставка невозможна, отправитель задан неправильно. Статус окончательный',
            'err_dest_invalid' => 'Доставка невозможна, указан неправильный номер. Статус окончательный',
            'err_skip_letter' => 'Доставка невозможна, т.к. во время отправки был изменён статус телефона, либо телефон был удалён из списка, либо письмо было удалено. Статус окончательный',
            'err_not_allowed' => 'Доставка невозможна, этот оператор связи не обслуживается. Статус окончательный',
            'err_delivery_failed' => 'Доставка не удалась - обычно по причине указания формально правильного, но несуществующего номера или из-за выключенного телефона. Статус окончательный',
            'err_lost' => 'Сообщение было утеряно, отправитель должен переотправить сообщение самостоятельно, т.к. оригинал не сохранился. Статус окончательный',
            'err_internal' => 'внутренний сбой. Необходима переотправка сообщения. Статус окончательный',
        ];
        foreach ($DistributionSmsInfos as $DistributionSmsInfo) {
            $result = json_decode($this->sendCheckSmsStatus($DistributionSmsInfo->getSmsId()));
            if (!empty($result->error)) {
                $message = "An error occured: " . isset($result->error) ? $result->error : ' ' . "(code: " . isset($result->code) ? $result->code : ' ' . ") " . $status_arr[$result->result->status];
            } else {
                $message = $status_arr[$result->result->status];
            }
            $DistributionSmsInfo->setSmsStatus($message);
            if ($this->em->persist($DistributionSmsInfo) && $this->em->flush($DistributionSmsInfo)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @param integer|string $sms_id
     *
     * @return bool|mixed
     */
    public function sendCheckSmsStatus($sms_id)
    {
        if ($curl = curl_init()) {
            $uniSender = $this->em->getRepository('AppBundle:Unisender')->findOneBy(['active' => '1']);
            // массив передаваемых параметров
            $parameters_array = array(
                'api_key' => $uniSender->getApiKey(),
                'sms_id' => $sms_id
            );

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $parameters_array);
            curl_setopt($curl, CURLOPT_TIMEOUT, 10);
            curl_setopt($curl, CURLOPT_URL, 'http://api.unisender.com/ru/api/checkSms?format=json');
            $response = curl_exec($curl);

            return $response;
        } else {
            return false;
        }
    }

}
