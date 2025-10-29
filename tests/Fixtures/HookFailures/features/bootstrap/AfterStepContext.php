<?php

declare(strict_types=1);

namespace HookFailures\Features\Bootstrap;

use Behat\Hook\AfterStep;

final class AfterStepContext extends BaseContext
{
    #[AfterStep]
    public function afterStepHook(): void
    {
        self::throwFailure('Error in afterStep hook');
    }
}

