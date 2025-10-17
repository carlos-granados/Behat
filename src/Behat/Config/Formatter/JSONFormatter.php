<?php

declare(strict_types=1);

namespace Behat\Config\Formatter;

use PhpParser\Node\Expr;

final class JSONFormatter extends Formatter
{
    public const NAME = 'json';

    public function __construct(...$baseOptions)
    {
        parent::__construct(name: self::NAME, settings: $baseOptions);
    }

    /**
     * @internal
     */
    public function toPhpExpr(): Expr
    {
        return $this->toPhpExprForNamedFormatter();
    }
}
