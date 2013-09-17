<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(), // Used to translate and upload on Entities
            new A2lix\TranslationFormBundle\A2lixTranslationFormBundle(), // Used to create multilingual forms/fields
            new FOS\UserBundle\FOSUserBundle(), // Used to handle user management
            new FOS\FacebookBundle\FOSFacebookBundle(), // Used to handle Facebook connect
        		new HWI\Bundle\OAuthBundle\HWIOAuthBundle(), // Used to handle OAuth connect (Twitter)
        		new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(), // Used to paginate results
            new Knp\Bundle\MenuBundle\KnpMenuBundle(), // Used to generate menus dynamically
            new JMS\I18nRoutingBundle\JMSI18nRoutingBundle(), // Used to translate routes
            new JMS\TranslationBundle\JMSTranslationBundle(), // Used to handle some translations
            new Craue\FormFlowBundle\CraueFormFlowBundle(), // Used for multi steps form
        		new Trsteel\CkeditorBundle\TrsteelCkeditorBundle(), // Used to handle CKeditor
        		new FM\ElfinderBundle\FMElfinderBundle(), // Used to handle file upload via CKeditor
            new LittleBigJoe\Bundle\BackendBundle\LittleBigJoeBackendBundle(),
            new LittleBigJoe\Bundle\FrontendBundle\LittleBigJoeFrontendBundle(),
            new LittleBigJoe\Bundle\CoreBundle\LittleBigJoeCoreBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
