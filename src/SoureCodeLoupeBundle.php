<?php

namespace SoureCode\Bundle\Loupe;

use Loupe\Loupe\LoupeFactory;
use SoureCode\Bundle\Loupe\Factory\DocumentFactoryInterface;
use SoureCode\Bundle\Loupe\Mapping\ClassMetadataFactory;
use SoureCode\Bundle\Loupe\Mapping\Driver\AttributeDriver;
use SoureCode\Bundle\Loupe\Provider\LoupeProvider;
use SoureCode\Bundle\Loupe\Provider\LoupeProviderInterface;
use SoureCode\Bundle\Loupe\Writer\LoupeWriter;
use SoureCode\Bundle\Loupe\Writer\LoupeWriterInterface;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Contracts\Cache\CacheInterface;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

class SoureCodeLoupeBundle extends AbstractBundle
{
    private static string $PREFIX = 'soure_code.loupe.';

    public function configure(DefinitionConfigurator $definition): void
    {
        // @formatter:off
        $definition->rootNode()
            ->fixXmlConfig('loupe')
            ->children()
                ->scalarNode('storage')
                    ->defaultValue('%kernel.project_dir%/var/loupe')
                ->end()
                ->scalarNode('cache')
                    ->defaultValue(CacheInterface::class)
                ->end()
                ->scalarNode('driver')
                    ->defaultValue('soure_code.loupe.driver.attribute')
                ->end()
                ->arrayNode('classes')
                    ->scalarPrototype()->end()
                    ->validate()
                        ->ifTrue(fn (array $classes) => array_filter($classes, static fn ($class) => !class_exists($class)))
                        ->thenInvalid('The class "%s" does not exist.')
                    ->end()
                ->end()
            ->end()
        ;
        // @formatter:on
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $parameters = $container->parameters();

        $parameters->set(self::$PREFIX.'classes', $config['classes']);

        $services = $container->services();

        $services->set(self::$PREFIX.'driver.attribute', AttributeDriver::class);

        $services->alias(self::$PREFIX.'driver', $config['driver']);
        $services->alias(self::$PREFIX.'cache', $config['cache']);

        $services->set(self::$PREFIX.'metadata_factory', ClassMetadataFactory::class)
            ->args([
                service(self::$PREFIX.'cache'),
                service(self::$PREFIX.'driver'),
                param(self::$PREFIX.'classes'),
            ]);

        $services->set(self::$PREFIX.'loupe_factory', LoupeFactory::class);

        $services->set(self::$PREFIX.'loupe_provider', LoupeProvider::class)
            ->args([
                $config['storage'],
                param('kernel.environment'),
                service(self::$PREFIX.'loupe_factory'),
                service(self::$PREFIX.'metadata_factory'),
            ]);

        $services->alias(LoupeProviderInterface::class, self::$PREFIX.'loupe_provider')
            ->public();

        $services->set(self::$PREFIX.'loupe_writer', LoupeWriter::class)
            ->args([
                service(self::$PREFIX.'loupe_provider'),
                tagged_iterator(DocumentFactoryInterface::class, indexAttribute: 'index'),
            ]);

        $services->alias(LoupeWriterInterface::class, self::$PREFIX.'loupe_writer')
            ->public();
    }
}
