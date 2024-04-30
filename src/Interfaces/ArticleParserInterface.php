<?php

namespace App\Readers;

use Feed;

interface ArticleParserInterface
{
    public function parse(string $content): string;

}
