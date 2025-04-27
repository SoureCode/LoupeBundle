<?php

namespace SoureCode\Bundle\Loupe\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
final readonly class Document
{
    public function __construct(
        public ?string $indexName = null,
        public ?int $maxQueryTokens = null,
        public ?int $minTokenLengthForPrefixSearch = null,
        public ?array $rankingRules = null,
        public ?array $stopWords = null,
    ) {
    }
}
