<?php

namespace SoureCode\Bundle\Loupe\Mapping;

use Loupe\Loupe\Config\TypoTolerance;
use Loupe\Loupe\Configuration;

class ClassMetadata
{
    /**
     * @var class-string
     */
    public string $className;
    public string $indexName = '';
    public string $primaryKey = 'id';
    public array $searchableFields = [];
    public array $sortableFields = [];
    public array $filterableFields = [];
    public array $languages = [];
    public int $maxQueryTokens = 10;
    public int $minTokenLengthForPrefixSearch = 3;
    public array $rankingRules = [
        'words',
        'typo',
        'proximity',
        'attribute',
        'exactness',
    ];
    public array $stopWords = [];

    // Typo Tolerance
    public int $alphabetSize = 4;
    public bool $firstCharTypoCountsDouble = true;
    public int $indexLength = 14;
    public bool $isDisabled = false;
    public bool $isEnabledForPrefixSearch = false;
    public array $typoThresholds = [
        9 => 2,
        5 => 1,
    ];

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function getConfiguration(): Configuration
    {
        return (new Configuration())
            ->withPrimaryKey($this->primaryKey)
            ->withSearchableAttributes($this->searchableFields)
            ->withFilterableAttributes($this->filterableFields)
            ->withSortableAttributes($this->sortableFields)
            ->withLanguages($this->languages)
            ->withMaxQueryTokens($this->maxQueryTokens)
            ->withMinTokenLengthForPrefixSearch($this->minTokenLengthForPrefixSearch)
            ->withRankingRules($this->rankingRules)
            ->withStopWords($this->stopWords)
            ->withTypoTolerance($this->getTypoTolerance());
    }

    private function getTypoTolerance(): TypoTolerance
    {
        $typoTolerance = (new TypoTolerance())
            ->withAlphabetSize($this->alphabetSize)
            ->withFirstCharTypoCountsDouble($this->firstCharTypoCountsDouble)
            ->withIndexLength($this->indexLength)
            ->withEnabledForPrefixSearch($this->isEnabledForPrefixSearch)
            ->withTypoThresholds($this->typoThresholds);

        if ($this->isDisabled) {
            return $typoTolerance->disable();
        }

        return $typoTolerance;
    }
}
