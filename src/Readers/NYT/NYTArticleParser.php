<?php

namespace App\Readers\NYT;

use App\Readers\ArticleParserInterface;
use Exception;

class NYTArticleParser implements ArticleParserInterface
{

    public function parse(string $content): string
    {
        $data = $this->extractData($content);
        $article = $this->parseContent($data);
        return implode("\n\n", $article) . "\n";
    }

    private function extractData(string $content): string
    {
        preg_match("/.*<script>window.__preloadedData = (.*);<\/script>.*/", $content, $match);
        return str_replace([":undefined"], [':"undefined"'], $match[1]);
    }

    /** @throws Exception */
    private function parseContent(string $content): array
    {
        try {
            $data = json_decode($content, true);
            $content = $data['initialData']['data']['article']['sprinkledBody']['content'] ??= [];

            $mainContent = [];
            foreach ($content as $item) {
                if (in_array($item["__typename"], ["ParagraphBlock"])) {
                    $buffer = '';
                    foreach ($item['content'] as $elem) {
                        $buffer .= $elem['text'];
                        if ($buffer[-1] !== '.') {
                            continue;
                        }

                        $mainContent[] = $buffer;
                        $buffer = '';
                    }

                    if ($buffer !== '') {
                        $mainContent[] = $buffer;
                    }
                }
            }
            return $mainContent;
        } catch (\Throwable $e) {
            throw new Exception("Failed to parse article content: " . $e->getMessage());
        }
    }

}