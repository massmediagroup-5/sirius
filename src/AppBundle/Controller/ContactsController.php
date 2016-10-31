<?php

namespace AppBundle\Controller;

use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class ContactsController extends Controller
{

    /**
     * @Route("/contacts", name="contacts")
     */
    public function indexAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('name', 'text')
            ->add('email', 'email')
            ->add('message', 'textarea')
            ->add('recaptcha', EWZRecaptchaType::class,
                [
                    'label'       => 'Captcha check:',
                    'mapped'      => false,
                    'constraints' => [
                        new Constraints\IsTrue()
                    ],
                    'attr'        => [
                        'options' => [
                            'theme'  => 'default',
                            'type'  => 'image',
                            'defer' => false,
                            'async' => false,
                            'size'  => 2
                        ]
                    ]
                ])
            ->add('submit', 'submit')
            ->getForm();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $message = \Swift_Message::newInstance()
                    ->setSubject('Message from contacts@sirius-sport.com')
                    ->setFrom('contacts@sirius-sport.com')
                    ->addTo($this->get('options')->getParamValue('email'))
                    ->setBody('<h2>Собщение с раздела Контакты</h2><hr>' .
                        '<p>Имя: ' . $data['name'] . '</p>' .
                        '<p>E-mail: ' . $data['email'] . '</p>' .
                        '<p>Сообщение: ' . $data['message'] . '</p>'
                    )
                    ->setContentType("text/html");
                $this->container->get('mailer')->send($message);
                $this->addFlash(
                    'success',
                    'Спасибо, в ближайшее время с вами свяжется наш менеджер'
                );

                return $this->redirect($request->getUri());
            }
        }


        return $this->render('AppBundle:contacts.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir') . '/..'),
            'form' => $form->createView()
        ));
    }

}
