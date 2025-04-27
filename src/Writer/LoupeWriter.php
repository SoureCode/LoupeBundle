<?php

namespace SoureCode\Bundle\Loupe\Writer;

use SoureCode\Bundle\Loupe\Factory\DocumentFactoryInterface;
use SoureCode\Bundle\Loupe\Provider\LoupeProviderInterface;

class LoupeWriter implements LoupeWriterInterface
{
    /**
     * @var array<string, DocumentFactoryInterface>
     */
    private array $documentFactories;

    public function __construct(
        private readonly LoupeProviderInterface $loupeProvider,
        iterable $documentFactories,
    ) {
        $this->documentFactories = iterator_to_array($documentFactories);
    }

    public function write(object|array $objects, ?string $locale = null): void
    {
        $locale = $locale ?? 'unlocalized';

        if (!is_iterable($objects)) {
            $objects = [$objects];
        }

        if (empty($objects)) {
            return;
        }

        $loupe = $this->loupeProvider->get($objects[0]::class, $locale);
        $documentFactory = $this->getDocumentFactory($objects[0]::class);

        $documents = [];

        foreach ($objects as $object) {
            $documents[] = $documentFactory->create($object, $locale);
        }

        if (\count($documents) > 0) {
            $loupe->addDocuments($documents);
        }
    }

    private function getDocumentFactory(string $class)
    {
        if (isset($this->documentFactories[$class])) {
            return $this->documentFactories[$class];
        }

        throw new \RuntimeException(\sprintf('No document factory found for class "%s".', $class));
    }
}
