<?php

declare(strict_types=1);

namespace HookFailures\Features\Bootstrap;

use Behat\Hook\AfterFeature;

final class AfterFeatureContext extends BaseContext
{
    #[AfterFeature]
    public static function afterFeatureHook(): void
    {
        self::throwFailure('Error in afterFeature hook');
    }
}

