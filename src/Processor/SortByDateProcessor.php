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

namespace FastFeed\Processor;

/**
 * SortByDateProcessor
 * Sort
 */
class SortByDateProcessor implements ProcessorInterface
{
    /**
     * Execute processor
     *
     * @param array $items
     *
     * @return array
     */
    public function process(array $items): array
    {
        usort($items, function ($a, $b) {
            $dateA = $a->getDate();
            $dateB = $b->getDate();
            if (!$dateA || !$dateB) {
                return 0;
            }
            $tA = $dateA->getTimestamp();
            $tB = $dateB->getTimestamp();
            if ($tA === $tB) {
                return 0;
            }
            return ($tA > $tB) ? -1 : 1;
        });

        return $items;
    }
}
