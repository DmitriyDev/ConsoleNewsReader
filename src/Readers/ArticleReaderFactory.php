<?php

namespace App\Readers;

use App\Readers\NYT\NYTArticleParser;
use App\Readers\NYT\NYTArticleReader;
use GuzzleHttp\Client;

class ArticleReaderFactory
{
    public function getArticleReader(SourceEnum $source): ArticleReaderInterface
    {
        switch ($source) {
            case SourceEnum::NY_TIMES:
            {
                return new NYTArticleReader(
                    new Client(),
                    new NYTArticleParser(),
                );
            }
            default:
                throw new \InvalidArgumentException("Unsupported source type");
        }
    }

}