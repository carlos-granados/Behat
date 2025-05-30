<?php

/*
 * This file is part of the Behat Testwork.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Testwork\Hook\Call;

use Behat\Testwork\Call\Exception\BadCallbackException;
use Behat\Testwork\Hook\Scope\HookScope;
use Behat\Testwork\Hook\Scope\SuiteScope;
use Behat\Testwork\Suite\Suite;

/**
 * Represents suite hook executed in the runtime.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
abstract class RuntimeSuiteHook extends RuntimeFilterableHook
{
    /**
     * Initializes hook.
     *
     * @param string                               $scopeName
     * @param string|null                          $filterString
     * @param callable|array{class-string, string} $callable
     * @param string|null                          $description
     *
     * @throws BadCallbackException If callback is method, but not a static one
     */
    public function __construct($scopeName, $filterString, $callable, $description = null)
    {
        parent::__construct($scopeName, $filterString, $callable, $description);

        $this->throwIfInstanceMethod($callable, 'Suite');
    }

    public function filterMatches(HookScope $scope)
    {
        if (!$scope instanceof SuiteScope) {
            return false;
        }
        if (null === ($filterString = $this->getFilterString())) {
            return true;
        }

        if (!empty($filterString)) {
            return $this->isSuiteMatch($scope->getSuite(), $filterString);
        }

        return false;
    }

    /**
     * Checks if Feature matches specified filter.
     *
     * @param string $filterString
     *
     * @return bool
     */
    private function isSuiteMatch(Suite $suite, $filterString)
    {
        if ('/' === $filterString[0]) {
            return 1 === preg_match($filterString, $suite->getName());
        }

        return false !== mb_strpos($suite->getName(), $filterString, 0, 'utf8');
    }
}
