<?php

namespace SoureCode\Bundle\Loupe\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
final readonly class TypoTolerance
{
    public function __construct(
        public ?int $alphabetSize = null,
        public ?bool $firstCharTypoCountsDouble = null,
        public ?int $indexLength = null,
        public ?bool $isDisabled = null,
        public ?bool $isEnabledForPrefixSearch = null,
        public ?array $typoThresholds = null,
    ) {
    }
}
