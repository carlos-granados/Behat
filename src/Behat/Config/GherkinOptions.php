<?php

namespace Behat\Config;

use Behat\Config\Converter\ConfigConverterTools;
use Behat\Config\Filter\FilterInterface;
use Behat\Config\Filter\NameFilter;
use Behat\Config\Filter\NarrativeFilter;
use Behat\Config\Filter\RoleFilter;
use Behat\Config\Filter\TagFilter;
use Behat\Testwork\ServiceContainer\Exception\ConfigurationLoadingException;
use PhpParser\Node\Expr;

final class GherkinOptions implements ConfigConverterInterface
{
    private const CACHE_SETTING = 'cache';
    private const FILTERS_SETTING = 'filters';

    private const CACHE_FUNCTION = 'withCacheDir';
    private const FILTER_FUNCTION = 'withFilter';

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

    public function withFilter(FilterInterface $filter): self
    {
        if (array_key_exists($filter->name(), $this->settings[self::FILTERS_SETTING] ?? [])) {
            throw new ConfigurationLoadingException(sprintf('The filter "%s" already exists.', $filter->name()));
        }

        $this->settings[self::FILTERS_SETTING][$filter->name()] = $filter->value();

        return $this;
    }

    /**
     * @internal
     */
    public function toPhpExpr(): Expr
    {
        $optionsObject = ConfigConverterTools::createObject(self::class);
        $expr = $optionsObject;

        $this->addCacheToExpr($expr);
        $this->addFiltersToExpr($expr);

        $arguments = count($this->settings) === 0 ? [] : [$this->settings];
        ConfigConverterTools::addArgumentsToConstructor($arguments, $optionsObject);

        return $expr;
    }

    private function addCacheToExpr(Expr &$expr): void
    {
        if (isset($this->settings[self::CACHE_SETTING])) {
            $expr = ConfigConverterTools::addMethodCall(
                self::class,
                self::CACHE_FUNCTION,
                [$this->settings[self::CACHE_SETTING]],
                $expr
            );
            unset($this->settings[self::CACHE_SETTING]);
        }
    }

    private function addFiltersToExpr(Expr &$expr): void
    {
        if (!isset($this->settings[self::FILTERS_SETTING])) {
            return;
        }

        foreach ($this->settings[self::FILTERS_SETTING] as $name => $filterValue) {
            $filter = match ($name) {
                NameFilter::NAME => new NameFilter($filterValue),
                NarrativeFilter::NAME => new NarrativeFilter($filterValue),
                RoleFilter::NAME => new RoleFilter($filterValue),
                TagFilter::NAME => new TagFilter($filterValue),
                default => null,
            };
            if ($filter !== null) {
                $expr = ConfigConverterTools::addMethodCall(
                    self::class,
                    self::FILTER_FUNCTION,
                    [$filter->toPhpExpr()],
                    $expr
                );
                unset($this->settings[self::FILTERS_SETTING][$name]);
            }
        }
        if ($this->settings[self::FILTERS_SETTING] === []) {
            unset($this->settings[self::FILTERS_SETTING]);
        }
    }
}
