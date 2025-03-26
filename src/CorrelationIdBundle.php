<?php namespace Ersatzhero\CorrelationIdBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class CorrelationIdBundle extends AbstractBundle {

    /**
     * @inheritDoc
     */
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->scalarNode('headerName')->defaultValue('X-CorrelationId')->end()
                ->scalarNode('attributeName')->defaultValue('correlationId')->end()
                ->scalarNode('logAttributeName')->defaultValue('correlationId')->end()
            ->end();
    }

    /**
     * @inheritDoc
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yaml');
        $params = $container->parameters();
        foreach ($config as $key => $value) {
          $params->set($this->extensionAlias . '.' . $key, $value);
        }
    }

}
