<?php

namespace App\Factory;

use App\Model\Post;
use SoureCode\Bundle\Loupe\Factory\DocumentFactoryInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * @implements DocumentFactoryInterface<Post>
 */
#[AutoconfigureTag(DocumentFactoryInterface::class, ['index' => Post::class])]
class PostDocumentFactory implements DocumentFactoryInterface
{
    /**
     * @psalm-param Post $object
     */
    public function create(object $object, ?string $locale = null): array
    {
        return [
            'id' => $object->id,
            'title' => $object->title,
            'content' => $object->content,
        ];
    }
}
