<?php

namespace Behat\Behat\Gherkin\Exception;

use Behat\Testwork\Exception\TestworkException;
use RuntimeException;

final class ParserNotAvailable extends RuntimeException implements TestworkException
{
}
