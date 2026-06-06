<?php

declare(strict_types=1);

/**
 * This file is part of the FastFeed package.
 *
 * Copyright (c) Daniel González
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Daniel González <daniel@desarrolla2.com>
 */

namespace FastFeed;

use Desarrolla2\Cache\CacheInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use FastFeed\Exception\LogicException;
use FastFeed\Parser\ParserInterface;
use FastFeed\Processor\ProcessorInterface;

/**
 * FastFeed
 */
class FastFeed implements FastFeedInterface
{
    /**
     * @const VERSION
     */
    const VERSION = '0.1';

    /**
     * @const USER_AGENT
     */
    const USER_AGENT = 'FastFeed/FastFeed';

    /**
     * @var ClientInterface;
     */
    protected $http;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var CacheInterface|null
     */
    protected $cache;

    /**
     * @var array
     */
    protected $parsers = array();

    /**
     * @var array
     */
    protected $processors = array();

    /**
     * @var array
     */
    protected $feeds = array();

    public function __construct(ClientInterface $http, LoggerInterface $logger)
    {
        $this->http = $http;
        $this->logger = $logger;
    }

    /**
     * Add feed to channel
     *
     * @param string $channel
     * @param string $feed
     *
     * @throws LogicException
     */
    public function addFeed(string $channel, string $feed): void
    {
        if (!filter_var($feed, FILTER_VALIDATE_URL)) {
            throw new LogicException('You tried to add a invalid url.');
        }
        $this->feeds[$channel][] = $feed;
    }

    /**
     * @param string $channel
     * @return Item[]
     * @throws Exception\LogicException
     * @throws GuzzleException
     */
    public function fetch(string $channel = 'default'): array
    {
        if (!isset($this->feeds[$channel])) {
            throw new LogicException('You tried to fetch a not existent channel');
        }

        $items = $this->retrieve($channel);

        foreach ($this->processors as $processor) {
            $items = $processor->process($items);
        }

        return $items;
    }

    /**
     * Retrieve a channel
     *
     * @param string $channel
     *
     * @return array
     * @throws LogicException
     */
    public function getFeed(string $channel): array
    {
        if (!isset($this->feeds[$channel])) {
            throw new LogicException('You tried to get a not existent channel');
        }

        return $this->feeds[$channel];
    }

    /**
     * @return ParserInterface
     * @throws Exception\LogicException
     */
    public function popParser(): ParserInterface
    {
        if (!$this->parsers) {
            throw new LogicException('You tried to pop from an empty parsers stack.');
        }

        return array_shift($this->parsers);
    }

    /**
     * @param ParserInterface $parser
     */
    public function pushParser(ParserInterface $parser): void
    {
        $this->parsers[] = $parser;
    }

    /**
     * @return ProcessorInterface
     * @throws Exception\LogicException
     */
    public function popProcessor(): ProcessorInterface
    {
        if (!$this->processors) {
            throw new LogicException('You tried to pop from an empty Processor stack.');
        }

        return array_shift($this->processors);
    }

    /**
     * @param ProcessorInterface $processor
     */
    public function pushProcessor(ProcessorInterface $processor): void
    {
        $this->processors[] = $processor;
    }

    /**
     * Retrieve all channels
     *
     * @return array
     */
    public function getFeeds(): array
    {
        return $this->feeds;
    }

    /**
     * Set Guzzle
     *
     * @param ClientInterface $guzzle
     */
    public function setHttpClient(ClientInterface $guzzle): void
    {
        $this->http = $guzzle;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @param CacheInterface $cache
     */
    public function setCache(CacheInterface $cache): void
    {
        $this->cache = $cache;
    }

    /**
     * @return CacheInterface|null
     */
    public function getCache(): ?CacheInterface
    {
        return $this->cache;
    }

    /**
     * Set a channel
     *
     * @param string $channel
     * @param string $feed
     *
     * @throws LogicException
     */
    public function setFeed(string $channel, string $feed): void
    {
        $this->feeds[$channel] = array();
        $this->addFeed($channel, $feed);
    }

    /**
     * Retrieve content from a resource
     * @param string $url
     * @return ?string
     * @throws GuzzleException
     */
    protected function get($url)
    {
        $headers = [];
        $cacheKey = 'ff_http_' . md5($url);
        $cached = null;

        if ($this->cache) {
            try {
                if ($this->cache->has($cacheKey)) {
                    $cached = $this->cache->get($cacheKey);
                }
            } catch (\Exception $e) {
                // Ignore cache failures
            }
        }

        if (is_array($cached)) {
            if (!empty($cached['etag'])) {
                $headers['If-None-Match'] = $cached['etag'];
            }
            if (!empty($cached['last_modified'])) {
                $headers['If-Modified-Since'] = $cached['last_modified'];
            }
        }

        $response = $this->http->request('GET', $url, [
            'headers' => $headers,
            'http_errors' => false,
        ]);

        $statusCode = $response->getStatusCode();

        if ($statusCode === 304 && is_array($cached)) {
            $this->logger->log(LogLevel::INFO, 'retrieved url "'.$url.'" (304 Not Modified)');
            return $cached['content'];
        }

        if ($statusCode < 200 || $statusCode >= 300) {
            $this->log('fail with '.$statusCode.' http code in url "'.$url.'" ');
            return null;
        }

        $content = $response->getBody()->getContents();
        $this->logger->log(LogLevel::INFO, 'retrieved url "'.$url.'" ');

        if ($this->cache) {
            $etag = $response->getHeaderLine('ETag');
            $lastModified = $response->getHeaderLine('Last-Modified');
            if ($etag || $lastModified) {
                try {
                    $this->cache->set($cacheKey, [
                        'etag' => $etag,
                        'last_modified' => $lastModified,
                        'content' => $content,
                    ]);
                } catch (\Exception $e) {
                    // Ignore cache set failures
                }
            }
        }

        return $content;
    }

    /**
     * @param string $channel
     * @return array
     * @throws GuzzleException
     */
    protected function retrieve($channel)
    {
        $result = array();

        foreach ($this->feeds[$channel] as $feed) {
            $content = $this->get($feed);
            if (!$content) {
                continue;
            }
            $result = array_merge($result, $this->parse($content));
        }

        return $result;
    }

    /**
     * @param $content
     *
     * @return array
     */
    protected function parse($content)
    {
        $result = array();
        foreach ($this->parsers as $parser) {
            $nodes = $parser->getNodes($content);
            if (!$nodes) {
                continue;
            }

            foreach ($nodes as $node) {
                $result[] = $node;
            }
        }

        return $result;
    }

    /**
     * @param $message
     */
    protected function log($message)
    {
        $this->logger->log(
            LogLevel::INFO,
            '['.self::USER_AGENT.' v.'.self::VERSION.'] - '.$message
        );
    }
}
