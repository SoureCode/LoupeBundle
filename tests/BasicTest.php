<?php

use App\Model\Post;
use Loupe\Loupe\SearchParameters;
use Nyholm\BundleTest\TestKernel;
use SoureCode\Bundle\Loupe\Provider\LoupeProviderInterface;
use SoureCode\Bundle\Loupe\SoureCodeLoupeBundle;
use SoureCode\Bundle\Loupe\Writer\LoupeWriterInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class BasicTest extends KernelTestCase
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

    public function testBasicUsage(): void
    {
        // Arrange
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();
        /**
         * @var LoupeProviderInterface $loupProvider
         */
        $loupProvider = $container->get(LoupeProviderInterface::class);
        /**
         * @var LoupeWriterInterface $writer
         */
        $writer = $container->get(LoupeWriterInterface::class);

        // Act
        $post = new Post();
        $post->id = 1;
        $post->title = 'Test Title';
        $post->content = 'Test Description';

        $writer->write($post, 'en');

        $loup = $loupProvider->get(Post::class, 'en');

        $searchParameters = (new SearchParameters())
            ->withQuery('Test');

        $result = $loup->search($searchParameters);

        // Assert
        dump($result);
        $this->assertNotEmpty($result->getHits());
    }
}
