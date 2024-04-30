<?php

namespace App\Readers;

interface ArticleReaderInterface
{
    public function readArticle(string $url): string;
}
