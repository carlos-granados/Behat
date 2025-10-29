<?php

declare(strict_types=1);

namespace HookFailures\Features\Bootstrap;

use Behat\Config\Config;
use Behat\Config\Profile;

return (new Config())
    ->withProfile((new Profile('beforeSuite'))
        ->withContexts(
            HookFailures\Features\Bootstrap\BeforeSuiteContext::class
        )
    )
    ->withProfile((new Profile('afterSuite'))
        ->withContexts(
            HookFailures\Features\Bootstrap\AfterSuiteContext::class
        )
    )
    ->withProfile((new Profile('beforeFeature'))
        ->withContexts(
            HookFailures\Features\Bootstrap\BeforeFeatureContext::class
        )
    )
    ->withProfile((new Profile('afterFeature'))
        ->withContexts(
            HookFailures\Features\Bootstrap\AfterFeatureContext::class
        )
    )
    ->withProfile((new Profile('beforeScenario'))
        ->withContexts(
            HookFailures\Features\Bootstrap\BeforeScenarioContext::class
        )
    )
    ->withProfile((new Profile('afterScenario'))
        ->withContexts(
            HookFailures\Features\Bootstrap\AfterScenarioContext::class
        )
    )
    ->withProfile((new Profile('beforeStep'))
        ->withContexts(
            HookFailures\Features\Bootstrap\BeforeStepContext::class
        )
    )
    ->withProfile((new Profile('afterStep'))
        ->withContexts(
            HookFailures\Features\Bootstrap\AfterStepContext::class
        )
    )
;
