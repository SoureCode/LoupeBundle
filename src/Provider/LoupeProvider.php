<?php

namespace SoureCode\Bundle\Loupe\Provider;

use Loupe\Loupe\Loupe;
use Loupe\Loupe\LoupeFactory;
use SoureCode\Bundle\Loupe\Mapping\ClassMetadataFactory;
use Symfony\Component\Filesystem\Path;
use Symfony\Contracts\Service\ResetInterface;

final class LoupeProvider implements LoupeProviderInterface, ResetInterface
{
    private array $instances = [];

    public function __construct(
        private readonly string $storageDirectory,
        private readonly string $environment,
        private readonly LoupeFactory $factory,
        private readonly ClassMetadataFactory $classMetadataFactory,
    ) {
    }

    public function get(string $className, ?string $locale = null): Loupe
    {
        $locale = $locale ?? 'unlocalized';

        if (!isset($this->instances[$className][$locale])) {
            $classMetadata = $this->classMetadataFactory->getMetadataFor($className);

            $storageDirectory = Path::join(
                $this->storageDirectory,
                $this->environment,
                $classMetadata->indexName,
                $locale
            );

            $configuration = $classMetadata->getConfiguration();

            $this->instances[$className][$locale] = $this->factory->create($storageDirectory, $configuration);
        }

        return $this->instances[$className][$locale];
    }

    public function reset(): void
    {
        $this->instances = [];
    }
}
