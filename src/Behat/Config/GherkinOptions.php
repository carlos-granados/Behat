<?php

namespace Behat\Config;

use Behat\Config\Filter\FilterInterface;
use Behat\Gherkin\GherkinCompatibilityMode;
use Behat\Testwork\ServiceContainer\Exception\ConfigurationLoadingException;

final class GherkinOptions
{
    private const CACHE_SETTING = 'cache';
    private const COMPATIBILITY_SETTING = 'compatibility';
    private const FILTERS_SETTING = 'filters';

    public function __construct(
        private array $settings = [],
    ) {
    }

    public function toArray(): array
    {
        return $this->settings;
    }

    /**
     * Sets the parser cache directory (defaults to the system tmp dir, if writable).
     */
    public function withCacheDir(string $dir): self
    {
        $this->settings[self::CACHE_SETTING] = $dir;

        return $this;
    }

    /**
     * Controls the extent to which gherkin is parsed equivalent to other cucumber tools.
     *
     * In legacy mode (the default), feature files are parsed as they have been in previous versions of Behat. This
     * differs slightly from the behaviour of current versions of the official cucumber parsers and runners.
     *
     * Other modes will parse identical to the official cucumber parsers.
     */
    public function withCompatibilityMode(GherkinCompatibilityMode $mode): self
    {
        $this->settings[self::COMPATIBILITY_SETTING] = $mode->value;

        return $this;
    }

    public function withFilter(FilterInterface $filter): self
    {
        if (array_key_exists($filter->name(), $this->settings[self::FILTERS_SETTING] ?? [])) {
            throw new ConfigurationLoadingException(sprintf('The filter "%s" already exists.', $filter->name()));
        }

        $this->settings[self::FILTERS_SETTING][$filter->name()] = $filter->value();

        return $this;
    }
}
