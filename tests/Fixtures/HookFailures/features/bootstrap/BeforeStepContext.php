<?php

declare(strict_types=1);

namespace HookFailures\Features\Bootstrap;

use Behat\Hook\BeforeStep;

final class BeforeStepContext extends BaseContext
{
    #[BeforeStep]
    public function beforeStepHook(): void
    {
        self::throwFailure('Error in beforeStep hook');
    }
}

