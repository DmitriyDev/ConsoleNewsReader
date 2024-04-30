<?php

namespace App\Readers\NYT;

use App\Readers\ArticleParserInterface;
use App\Readers\ArticleReaderInterface;
use GuzzleHttp\ClientInterface;

readonly class NYTArticleReader implements ArticleReaderInterface
{
    public function __construct(
        private ClientInterface $client,
        private ArticleParserInterface $parser,
    ) {
    }

    public function readArticle(string $url): string
    {
        $content = $this->getContent($url);
        return $this->parser->parse($content);
    }

    /** @throws \Exception */
    private function getContent(string $url): string
    {
        try {
            $res = $this->client->request('GET', $url);
            return $res->getBody()->getContents();
        } catch (\Throwable $th) {
            throw new \Exception("Article Reader communication error: " . $th->getMessage());
        }
    }

}