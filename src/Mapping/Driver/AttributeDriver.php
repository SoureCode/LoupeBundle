<?php

namespace SoureCode\Bundle\Loupe\Mapping\Driver;

use SoureCode\Bundle\Loupe\Attributes as Search;
use SoureCode\Bundle\Loupe\Mapping\ClassMetadata;
use SoureCode\Bundle\Loupe\Mapping\ClassMetadataFactory;

class AttributeDriver implements DriverInterface
{
    public function load(string $className): ?ClassMetadata
    {
        $reflectionClass = new \ReflectionClass($className);

        $classMetadata = $this->loadDocumentAttribute($reflectionClass);

        if (null === $classMetadata) {
            return null;
        }

        $this->loadTypoToleranceAttribute($reflectionClass, $classMetadata);

        $reflectionProperties = $reflectionClass->getProperties();

        $primaryKeys = [];

        foreach ($reflectionProperties as $reflectionProperty) {
            $propertyAttributes = $reflectionProperty->getAttributes();
            if (\count($propertyAttributes) > 0) {
                foreach ($propertyAttributes as $propertyAttribute) {
                    if (Search\Searchable::class === $propertyAttribute->getName()) {
                        $classMetadata->searchableFields[] = $reflectionProperty->getName();
                    }

                    if (Search\Filterable::class === $propertyAttribute->getName()) {
                        $classMetadata->filterableFields[] = $reflectionProperty->getName();
                    }

                    if (Search\Sortable::class === $propertyAttribute->getName()) {
                        $classMetadata->sortableFields[] = $reflectionProperty->getName();
                    }

                    if (Search\PrimaryKey::class === $propertyAttribute->getName()) {
                        $classMetadata->primaryKey = $reflectionProperty->getName();
                        $primaryKeys[] = $reflectionProperty->getName();
                    }
                }
            }
        }

        if (\count($primaryKeys) > 1) {
            throw new \LogicException(\sprintf('Class %s has multiple PrimaryKey attributes. (properties: %s)', $reflectionClass->getName(), implode(', ', $primaryKeys)));
        }

        return $classMetadata;
    }

    private function loadDocumentAttribute(\ReflectionClass $reflectionClass): ?ClassMetadata
    {
        $documentAttribute = $reflectionClass->getAttributes(Search\Document::class);

        if (\count($documentAttribute) > 1) {
            throw new \RuntimeException(\sprintf('Class %s has multiple Document attributes', $reflectionClass->getName()));
        }

        if (0 === \count($documentAttribute)) {
            return null;
        }

        $classMetadata = new ClassMetadata($reflectionClass->getName());

        $documentAttribute = $documentAttribute[0]->newInstance();
        $classMetadata->indexName = ClassMetadataFactory::normalizeIndexName($documentAttribute->indexName ?? $reflectionClass->getShortName());

        if (null !== $documentAttribute->maxQueryTokens) {
            $classMetadata->maxQueryTokens = $documentAttribute->maxQueryTokens;
        }

        if (null !== $documentAttribute->minTokenLengthForPrefixSearch) {
            $classMetadata->minTokenLengthForPrefixSearch = $documentAttribute->minTokenLengthForPrefixSearch;
        }

        if (null !== $documentAttribute->rankingRules) {
            $classMetadata->rankingRules = $documentAttribute->rankingRules;
        }

        if (null !== $documentAttribute->stopWords) {
            $classMetadata->stopWords = $documentAttribute->stopWords;
        }

        return $classMetadata;
    }

    private function loadTypoToleranceAttribute(\ReflectionClass $reflectionClass, ClassMetadata $classMetadata): void
    {
        $typoToleranceAttribute = $reflectionClass->getAttributes(Search\TypoTolerance::class);

        if (\count($typoToleranceAttribute) > 1) {
            throw new \RuntimeException(\sprintf('Class %s has multiple TypoTolerance attributes', $reflectionClass->getName()));
        }

        if (0 === \count($typoToleranceAttribute)) {
            return;
        }

        $typoToleranceAttribute = $typoToleranceAttribute[0]->newInstance();

        if (null !== $typoToleranceAttribute->alphabetSize) {
            $classMetadata->alphabetSize = $typoToleranceAttribute->alphabetSize;
        }

        if (null !== $typoToleranceAttribute->firstCharTypoCountsDouble) {
            $classMetadata->firstCharTypoCountsDouble = $typoToleranceAttribute->firstCharTypoCountsDouble;
        }

        if (null !== $typoToleranceAttribute->indexLength) {
            $classMetadata->indexLength = $typoToleranceAttribute->indexLength;
        }

        if (null !== $typoToleranceAttribute->isDisabled) {
            $classMetadata->isDisabled = $typoToleranceAttribute->isDisabled;
        }

        if (null !== $typoToleranceAttribute->isEnabledForPrefixSearch) {
            $classMetadata->isEnabledForPrefixSearch = $typoToleranceAttribute->isEnabledForPrefixSearch;
        }

        if (null !== $typoToleranceAttribute->typoThresholds) {
            $classMetadata->typoThresholds = $typoToleranceAttribute->typoThresholds;
        }
    }
}
