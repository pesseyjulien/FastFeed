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

use DateTime;

/**
 * Node
 */
class Item
{

    protected ?string $itemId = null;
    protected ?string $name = null;
    protected ?string $intro = null;
    protected ?string $content = null;
    protected ?string $source = null;
    protected ?string $author = null;
    protected ?string $image = null;
    protected ?DateTime $date = null;
    protected array $extra = [];
    protected array $tags = [];

    /**
     * @param string $itemId
     */
    public function setId(string $itemId): void
    {
        $this->itemId = $itemId;
    }

    public function getId(): string
    {
        return $this->itemId;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setIntro(string $intro): void
    {
        $this->intro = $intro;
    }

    public function getIntro(): ?string
    {
        return $this->intro;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function hasImage(): bool
    {
        return $this->image !== null && strlen($this->image) > 0;
    }

    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function addTag(string $tag): void
    {
        $this->tags[] = $tag;
    }

    public function setTags(array $tags): void
    {
        $this->tags = array();
        foreach ($tags as $tag) {
            $this->addTag((string) $tag);
        }
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setSource(string $source): void
    {
        $this->source = $source;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setExtra(string $key, $value): void
    {
        $this->extra[$key] = $value;
    }

    public function getExtra(string $key): mixed
    {
        if (!isset($this->extra[$key])) {
            return null;
        }

        return $this->extra[$key];
    }
}
