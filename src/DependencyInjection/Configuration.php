<?php

namespace Akimmaksimov85\CreatorBundle\DependencyInjection;

use Akimmaksimov85\CreatorBundle\CreatorBundle;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        return new TreeBuilder(CreatorBundle::CONFIG_BUNDLE_NAMESPACE);
    }
}