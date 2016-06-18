<?php
namespace NullDev\SkeletonBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class NullDevSkeletonExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('nulldev_skeleton.config', $config);
        /*

                if (array_key_exists('phpspec', $config) && $config['phpspec']['enabled'] === true) {
                    $container->setParameter('nulldev_skeleton.config.phpspec', $config['phpspec']);
                }else{
                    $container->setParameter('nulldev_skeleton.config.phpspec', $config['phpspec']);
                }
                if (array_key_exists('phpunit', $config) && $config['phpunit']['enabled'] === true) {
                    $container->setParameter('nulldev_skeleton.config.phpunit', $config['phpunit']);
                }

                // $container->setParameter('nulldev_skeleton.paths', $config['paths']);
        */
    }

    public function getAlias()
    {
        return 'nulldev_skeleton';
    }
}
