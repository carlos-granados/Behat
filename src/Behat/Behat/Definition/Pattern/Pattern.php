<?php

/*
 * This file is part of the Behat.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Behat\Definition\Pattern;

/**
 * Step definition pattern.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
final class Pattern
{
    public function __construct(
        private readonly string $suggestedMethodName,
        private readonly string $pattern,
        private readonly int $placeholderCount = 0,
    ) {
    }

    /**
     * Returns canonical step text.
     *
     * @deprecated Use getSuggestedMethodName() instead. Will be removed in 4.0.
     */
    public function getCanonicalText(): string
    {
        @trigger_error('Pattern::getCanonicalText() is deprecated. Use getSuggestedMethodName() instead. It will be removed in 4.0.', E_USER_DEPRECATED);

        return $this->suggestedMethodName;
    }

    public function getSuggestedMethodName(): string
    {
        return $this->suggestedMethodName;
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function getPlaceholderCount(): int
    {
        return $this->placeholderCount;
    }
}
