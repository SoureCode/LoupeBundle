<?php

namespace SoureCode\Bundle\Loupe\Mapping;

use SoureCode\Bundle\Loupe\Mapping\Driver\DriverInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Contracts\Cache\CacheInterface;

use function Symfony\Component\String\u;

final class ClassMetadataFactory
{
    private static ?AsciiSlugger $slugger = null;

    public function __construct(
        private readonly CacheInterface $cache,
        private readonly DriverInterface $driver,
        private readonly array $classNames,
    ) {
    }

    public static function normalizeIndexName(string $indexName): string
    {
        $slugger = self::getSlugger();

        return $slugger
            ->slug(
                u($indexName)
                    ->snake()
                    ->toString()
            )
            ->lower()
            ->trimSuffix('-index')
            ->toString();
    }

    private static function getSlugger(): AsciiSlugger
    {
        if (null === self::$slugger) {
            self::$slugger = new AsciiSlugger();
        }

        return self::$slugger;
    }

    /**
     * @param class-string $className
     */
    public function getMetadataFor(string $className): ClassMetadata
    {
        if (!class_exists($className)) {
            throw new \InvalidArgumentException(\sprintf('Class "%s" does not exist.', $className));
        }

        if (!\in_array($className, $this->classNames, true)) {
            throw new \InvalidArgumentException(\sprintf('Class "%s" is not registered.', $className));
        }

        $key = \sprintf('soure_code.loupe.metadata.%s', $className);

        return $this->cache->get(
            $key,
            function () use ($className) {
                $metadata = $this->driver->load($className);

                if (null === $metadata) {
                    throw new \InvalidArgumentException(\sprintf('No metadata found for class "%s".', $className));
                }

                return $metadata;
            }
        );
    }
}
