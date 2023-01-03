<?php

declare(strict_types = 1);

namespace Statistics\Calculator;

use SocialPost\Dto\SocialPostTo;
use Statistics\Dto\StatisticsTo;

class AverageUserPostsPerMonth extends AbstractCalculator
{
    protected const UNITS = 'posts';
    private array $countPostsByUser = [];

    /**
     * @inheritDoc
     */
    protected function doAccumulate(SocialPostTo $postTo): void
    {
        $this->countPostsByUser[$postTo->getAuthorId()] = ($this->countPostsByUser[$postTo->getAuthorId()] ?? 0) + 1;
    }

    /**
     * @inheritDoc
     */
    protected function doCalculate(): StatisticsTo
    {
        if (count($this->countPostsByUser) === 0) {
            return (new StatisticsTo());
        }

        $value = floor(array_sum($this->countPostsByUser) / count($this->countPostsByUser));

        return (new StatisticsTo())->setValue($value);
    }
}
