<?php

/*
 * This file is part of the Behat.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Behat\Transformation\Transformation;

use Behat\Behat\Definition\Call\DefinitionCall;
use Behat\Behat\Transformation\Call\TransformationCall;
use Behat\Behat\Transformation\SimpleArgumentTransformation;
use Behat\Testwork\Call\CallCenter;
use Behat\Testwork\Call\RuntimeCallee;
use ReflectionMethod;

/**
 * Token name based transformation.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
final class TokenNameTransformation extends RuntimeCallee implements SimpleArgumentTransformation
{
    public const PATTERN_REGEX = '/^\:\w+$/';

    /**
     * @var string
     */
    private $pattern;

    public static function supportsPatternAndMethod($pattern, ReflectionMethod $method)
    {
        return 1 === preg_match(self::PATTERN_REGEX, $pattern);
    }

    /**
     * Initializes transformation.
     *
     * @param string      $pattern
     * @param callable    $callable
     * @param string|null $description
     */
    public function __construct($pattern, $callable, $description = null)
    {
        $this->pattern = $pattern;

        parent::__construct($callable, $description);
    }

    public function supportsDefinitionAndArgument(DefinitionCall $definitionCall, $argumentIndex, $argumentArgumentValue)
    {
        return ':' . $argumentIndex === $this->pattern;
    }

    public function transformArgument(CallCenter $callCenter, DefinitionCall $definitionCall, $argumentIndex, $argumentValue)
    {
        $call = new TransformationCall(
            $definitionCall->getEnvironment(),
            $definitionCall->getCallee(),
            $this,
            [$argumentValue]
        );

        $result = $callCenter->makeCall($call);

        if ($result->hasException()) {
            throw $result->getException();
        }

        return $result->getReturn();
    }

    public function getPriority()
    {
        return 50;
    }

    public function getPattern()
    {
        return $this->pattern;
    }

    public function __toString()
    {
        return 'TokenNameTransform ' . $this->pattern;
    }
}
