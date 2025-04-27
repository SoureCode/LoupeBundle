<?php

namespace SoureCode\Bundle\Loupe\Tests;

use Nyholm\BundleTest\TestKernel;
use SoureCode\Bundle\Loupe\Provider\LoupeProviderInterface;
use SoureCode\Bundle\Loupe\SoureCodeLoupeBundle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class BundleInitializationTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        /**
         * @var TestKernel $kernel
         */
        $kernel = parent::createKernel($options);
        $kernel->setTestProjectDir(__DIR__.'/app');
        $kernel->addTestBundle(SoureCodeLoupeBundle::class);
        $kernel->addTestConfig(__DIR__.'/app/config/config.yml');
        $kernel->handleOptions($options);

        return $kernel;
    }

    public function testInitBundle(): void
    {
        // Boot the kernel.
        $kernel = self::bootKernel();

        $container = $kernel->getContainer();

        $this->assertTrue($container->has(LoupeProviderInterface::class));
    }
}
