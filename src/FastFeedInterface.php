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

use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;

use FastFeed\Parser\ParserInterface;
use FastFeed\Processor\ProcessorInterface;

/**
 * FeedManagerInterface
 */
interface FastFeedInterface
{
    /**
     * Add feed to channel
     *
     * @param string $channel
     * @param string $feed
     *
     * @throws InvalidArgumentException
     */
    public function addFeed(string $channel, string $feed): void;

    /**
     * @param string $channel
     *
     * @return array
     * @throws Exception\InvalidArgumentException
     */
    public function fetch(string $channel = 'default'): array;

    /**
     * Retrieve a channel
     *
     * @param string $channel
     *
     * @return array
     * @throws LogicException
     */
    public function getFeed(string $channel): array;

    /**
     * @return ParserInterface
     * @throws Exception\LogicException
     */
    public function popParser(): ParserInterface;

    /**
     * @param ParserInterface $parser
     */
    public function pushParser(ParserInterface $parser): void;

    /**
     * @return ProcessorInterface
     * @throws Exception\LogicException
     */
    public function popProcessor(): ProcessorInterface;

    /**
     * @param ProcessorInterface $processor
     */
    public function pushProcessor(ProcessorInterface $processor): void;

    /**
     * Retrieve all channels
     *
     * @return array
     */
    public function getFeeds(): array;

    /**
     * Set Guzzle
     *
     * @param ClientInterface $guzzle
     */
    public function setHttpClient(ClientInterface $guzzle): void;

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void;

    /**
     * Set a channel
     *
     * @param string $channel
     * @param string $feed
     *
     * @throws InvalidArgumentException
     */
    public function setFeed(string $channel, string $feed): void;
}
