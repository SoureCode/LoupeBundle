<?php

namespace App\Model;

use SoureCode\Bundle\Loupe\Attributes as Search;

#[Search\Document()]
class Post
{
    #[Search\PrimaryKey()]
    public int $id;

    #[Search\Searchable()]
    public string $title;

    #[Search\Searchable()]
    public string $content;
}
