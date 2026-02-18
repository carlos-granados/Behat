<?php

namespace Behat\Testwork\ServiceContainer;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @internal
 */
final readonly class ExtensionCompilerPass implements CompilerPassInterface
{
    public function __construct(
        private Extension $extension,
    ) {
    }

    public function process(ContainerBuilder $container): void
    {
        $this->extension->process($container);
    }
}
