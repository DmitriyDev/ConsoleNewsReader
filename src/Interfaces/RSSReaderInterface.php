<?php

namespace App\Readers;

use Feed;

interface RSSReaderInterface
{
    public function read(string $url): Feed;

}
