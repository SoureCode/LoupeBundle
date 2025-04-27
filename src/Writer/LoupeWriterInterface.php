<?php

namespace SoureCode\Bundle\Loupe\Writer;

interface LoupeWriterInterface
{
    public function write(object|array $objects, ?string $locale = null): void;
}
