<?php

namespace Oro\Bundle\DataGridBundle\Extension\Toolbar;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration for create datagrid toolbar configuration tree
 * @package Oro\Bundle\DataGridBundle\Extension\Toolbar
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();

        $builder->root('toolbarOptions')
            ->children()
                ->booleanNode('hide')->defaultFalse()->end()
                ->booleanNode('addResetAction')->defaultTrue()->end()
                ->booleanNode('addRefreshAction')->defaultTrue()->end()
                ->booleanNode('addDatagridSettingsManager')->defaultTrue()->end()
                ->integerNode('turnOffToolbarRecordsNumber')->defaultValue(0)->end()
                ->arrayNode('pageSize')->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('hide')->defaultFalse()->end()
                        ->scalarNode('default_per_page')->defaultValue(10)->end()
                        ->arrayNode('items')
                            ->defaultValue([10, 25, 50, 100])
                            ->prototype('variable')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('pagination')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('hide')->defaultFalse()->end()
                        ->booleanNode('onePage')->defaultFalse()->end()
                    ->end()
                ->end()
                ->arrayNode('placement')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('top')->defaultTrue()->end()
                        ->booleanNode('bottom')->defaultFalse()->end()
                    ->end()
                ->end()
                ->arrayNode('datagridSettings')
                    ->children()
                    ->scalarNode('minVisibleColumnsQuantity')->end()
                ->end()
            ->end();

        return $builder;
    }
}
