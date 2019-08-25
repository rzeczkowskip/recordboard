<?php

namespace App\DependencyInjection\Compiler;

use App\Messenger\Middleware\PreprocessorMiddleware;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class MessengerPreprocessorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(PreprocessorMiddleware::class)) {
            return;
        }

        $preprocessorMiddleware = $container->getDefinition(PreprocessorMiddleware::class);
        $formatters = $container->findTaggedServiceIds('app.messenger.preprocessor');

        foreach ($formatters as $id => $tags) {
            $preprocessorMiddleware->addMethodCall('addPreprocessor', [new Reference($id)]);
        }
    }
}
