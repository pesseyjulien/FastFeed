<?php

use FastFeed\FastFeed;
use FastFeed\Logger\Logger;
use FastFeed\Parser\RSSParser;
use GuzzleHttp\Client;

$client = new Client();
$fastFeed = new FastFeed($client, new Logger(false));

$fastFeed->addFeed('desarrolla2', 'http://desarrolla2.com/feed/');
$fastFeed->pushParser(new RSSParser());
$ret = $fastFeed->fetch('desarrolla2');

var_dump($ret);die;
