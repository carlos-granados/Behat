<?php

/*
 * This file is part of the Behat Testwork.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Testwork\EventDispatcher\Tester;

use Behat\Testwork\EventDispatcher\Event\AfterExerciseCompleted;
use Behat\Testwork\EventDispatcher\Event\AfterExerciseSetup;
use Behat\Testwork\EventDispatcher\Event\BeforeExerciseCompleted;
use Behat\Testwork\EventDispatcher\Event\BeforeExerciseTeardown;
use Behat\Testwork\Tester\Exercise;
use Behat\Testwork\Tester\Result\TestResult;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Exercise dispatching BEFORE/AFTER events during its execution.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * @template TSpec
 *
 * @implements Exercise<TSpec>
 */
final class EventDispatchingExercise implements Exercise
{
    /**
     * @var Exercise<TSpec>
     */
    private $baseExercise;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * Initializes exercise.
     *
     * @param Exercise<TSpec>          $baseExercise
     */
    public function __construct(Exercise $baseExercise, EventDispatcherInterface $eventDispatcher)
    {
        $this->baseExercise = $baseExercise;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function setUp(array $iterators, $skip)
    {
        $event = new BeforeExerciseCompleted($iterators);

        $this->eventDispatcher->dispatch($event, $event::BEFORE);

        $setup = $this->baseExercise->setUp($iterators, $skip);

        $event = new AfterExerciseSetup($iterators, $setup);

        $this->eventDispatcher->dispatch($event, $event::AFTER_SETUP);

        return $setup;
    }

    public function test(array $iterators, $skip = false)
    {
        return $this->baseExercise->test($iterators, $skip);
    }

    public function tearDown(array $iterators, $skip, TestResult $result)
    {
        $event = new BeforeExerciseTeardown($iterators, $result);

        $this->eventDispatcher->dispatch($event, $event::BEFORE_TEARDOWN);

        $teardown = $this->baseExercise->tearDown($iterators, $skip, $result);

        $event = new AfterExerciseCompleted($iterators, $result, $teardown);

        $this->eventDispatcher->dispatch($event, $event::AFTER);

        return $teardown;
    }
}
