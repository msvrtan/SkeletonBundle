<?php
namespace NullDev\SkeletonBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('nulldev_skeleton');

        $rootNode
            ->children()
            ->arrayNode('code')
                ->children()
                    ->enumNode('autoload_type')->values(['psr0', 'psr4'])->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('path')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('prefix')->isRequired()->end()
                ->end()
            ->end()
            ->arrayNode('phpspec')
                ->canBeEnabled()
                ->children()
                    ->enumNode('autoload_type')->values(['psr0', 'psr4'])->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('path')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('prefix')->isRequired()->end()
                ->end()
            ->end()
            ->arrayNode('phpunit')
                ->canBeEnabled()
                ->children()
                    ->enumNode('autoload_type')->values(['psr0', 'psr4'])->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('path')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('prefix')->isRequired()->end()
                ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
