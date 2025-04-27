<?php

namespace SoureCode\Bundle\Loupe\Provider;

use Loupe\Loupe\Loupe;

interface LoupeProviderInterface
{
    public function get(string $className, ?string $locale = null): Loupe;
}
