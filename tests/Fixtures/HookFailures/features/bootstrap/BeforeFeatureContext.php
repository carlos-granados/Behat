<?php

declare(strict_types=1);

namespace HookFailures\Features\Bootstrap;

use Behat\Hook\BeforeFeature;

final class BeforeFeatureContext extends BaseContext
{
    #[BeforeFeature]
    public static function beforeFeatureHook(): void
    {
        self::throwFailure('Error in beforeFeature hook');
    }
}

