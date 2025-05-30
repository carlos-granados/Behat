<?php

/*
 * This file is part of the Behat.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Behat\Context\Reader;

use Behat\Behat\Context\Annotation\AnnotationReader;
use Behat\Behat\Context\Annotation\DocBlockHelper;
use Behat\Behat\Context\Environment\ContextEnvironment;
use Behat\Testwork\Call\Callee;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * Reads context callees by annotations using registered annotation readers.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
final class AnnotatedContextReader implements ContextReader
{
    public const DOCLINE_TRIMMER_REGEX = '/^\/\*\*\s*|^\s*\*\s*|\s*\*\/$|\s*$/';

    /**
     * @var string[]
     */
    private static $ignoreAnnotations = [
        '@param',
        '@return',
        '@throws',
        '@see',
        '@uses',
        '@todo',
    ];
    /**
     * @var AnnotationReader[]
     */
    private $readers = [];

    /**
     * @var DocBlockHelper
     */
    private $docBlockHelper;

    /**
     * Initializes reader.
     */
    public function __construct(DocBlockHelper $docBlockHelper)
    {
        $this->docBlockHelper = $docBlockHelper;
    }

    /**
     * Registers annotation reader.
     */
    public function registerAnnotationReader(AnnotationReader $reader)
    {
        $this->readers[] = $reader;
    }

    public function readContextCallees(ContextEnvironment $environment, $contextClass)
    {
        $reflection = new ReflectionClass($contextClass);

        $callees = [];
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            foreach ($this->readMethodCallees($reflection->getName(), $method) as $callee) {
                $callees[] = $callee;
            }
        }

        return $callees;
    }

    /**
     * Loads callees associated with specific method.
     *
     * @param string           $class
     *
     * @return Callee[]
     */
    private function readMethodCallees($class, ReflectionMethod $method)
    {
        $callees = [];

        // read parent annotations
        try {
            $prototype = $method->getPrototype();
            // error occurs on every second PHP stable release - getPrototype() returns itself
            if ($prototype->getDeclaringClass()->getName() !== $method->getDeclaringClass()->getName()) {
                $callees = array_merge($callees, $this->readMethodCallees($class, $prototype));
            }
        } catch (ReflectionException $e) {
        }

        if ($docBlock = $method->getDocComment()) {
            $callees = array_merge($callees, $this->readDocBlockCallees($class, $method, $docBlock));
        }

        return $callees;
    }

    /**
     * Reads callees from the method doc block.
     *
     * @param string           $class
     * @param string           $docBlock
     *
     * @return Callee[]
     */
    private function readDocBlockCallees($class, ReflectionMethod $method, $docBlock)
    {
        $callees = [];
        $description = $this->docBlockHelper->extractDescription($docBlock);
        $docBlock = $this->mergeMultilines($docBlock);

        foreach (explode("\n", $docBlock) as $docLine) {
            $docLine = preg_replace(self::DOCLINE_TRIMMER_REGEX, '', $docLine);

            if ($this->isEmpty($docLine)) {
                continue;
            }

            if ($this->isNotAnnotation($docLine)) {
                continue;
            }

            if ($callee = $this->readDocLineCallee($class, $method, $docLine, $description)) {
                $callees[] = $callee;
            }
        }

        return $callees;
    }

    /**
     * Merges multiline strings (strings ending with "\").
     *
     * @param string $docBlock
     *
     * @return string
     */
    private function mergeMultilines($docBlock)
    {
        return preg_replace("#\\\\$\s*+\*\s*+([^\\\\$]++)#m", '$1', $docBlock);
    }

    /**
     * Checks if provided doc lien is empty.
     *
     * @param string $docLine
     *
     * @return bool
     */
    private function isEmpty($docLine)
    {
        return '' == $docLine;
    }

    /**
     * Checks if provided doc line is not an annotation.
     *
     * @param string $docLine
     *
     * @return bool
     */
    private function isNotAnnotation($docLine)
    {
        return '@' !== substr($docLine, 0, 1);
    }

    /**
     * Reads callee from provided doc line using registered annotation readers.
     *
     * @param string      $class
     * @param string      $docLine
     * @param string|null $description
     *
     * @return Callee|null
     */
    private function readDocLineCallee($class, ReflectionMethod $method, $docLine, $description = null)
    {
        if ($this->isIgnoredAnnotation($docLine)) {
            return null;
        }

        foreach ($this->readers as $reader) {
            if ($callee = $reader->readCallee($class, $method, $docLine, $description)) {
                return $callee;
            }
        }

        return null;
    }

    /**
     * Checks if provided doc line is one of the ignored annotations.
     *
     * @param string $docLine
     *
     * @return bool
     */
    private function isIgnoredAnnotation($docLine)
    {
        $lowDocLine = strtolower($docLine);
        foreach (self::$ignoreAnnotations as $ignoredAnnotation) {
            if ($ignoredAnnotation == substr($lowDocLine, 0, strlen($ignoredAnnotation))) {
                return true;
            }
        }

        return false;
    }
}
