<?php

namespace AppAdminBundle\Block;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BaseBlockService;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class ImportBlockService extends BaseBlockService
{
    /**
     * @var AuthorizationChecker
     */
    protected $authorizationChecker;

    public function __construct($name, EngineInterface $templating, AuthorizationChecker $authorizationChecker)
    {
        parent::__construct($name, $templating);
        $this->authorizationChecker = $authorizationChecker;
    }

    public function getName()
    {
        return 'Import';
    }

    public function getDefaultSettings()
    {
        return [];
    }

    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
    }

    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
    }

    public function execute(BlockContextInterface $block, Response $response = null)
    {
        if ($this->authorizationChecker->isGranted('ROLE_SONATA_IMPORT')
            || $this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {

            // merge settings
            $settings = array_merge($this->getDefaultSettings(), $block->getSettings());

            return $this->renderResponse('AppAdminBundle:block:import.html.twig', array(
                'block' => $block->getBlock(),
                'settings' => $settings
            ), $response);
        }
        return new Response();
    }
}