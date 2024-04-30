<?php

namespace App\Readers;

use Feed;

class RSSReader implements RSSReaderInterface
{
    public function read(string $url): Feed
    {
        return Feed::loadRss($url);
    }
}