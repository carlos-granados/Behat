<?php

declare(strict_types=1);

namespace HookFailures\Features\Bootstrap;

use Behat\Behat\Context\Context;
use Behat\Step\When;
use Exception;

abstract class BaseContext implements Context
{
    #[When('I have a simple step')]
    public function iHaveASimpleStep(): void
    {
    }

    protected static function throwFailure(string $message): void
    {
        throw new Exception($message);
    }
}
