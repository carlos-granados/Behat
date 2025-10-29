<?php

declare(strict_types=1);

namespace HookFailures\Features\Bootstrap;

use Behat\Hook\BeforeScenario;

final class BeforeScenarioContext extends BaseContext
{
    #[BeforeScenario]
    public function beforeScenarioHook(): void
    {
        self::throwFailure('Error in beforeScenario hook');
    }
}

