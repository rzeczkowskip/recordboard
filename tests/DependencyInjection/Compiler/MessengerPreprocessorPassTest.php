<?php

namespace App\Tests\DependencyInjection\Compiler;

use App\DependencyInjection\Compiler\MessengerPreprocessorPass;
use App\Messenger\Middleware\PreprocessorMiddleware;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @group func
 */
class MessengerPreprocessorPassTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ContainerBuilder
     */
    private ContainerBuilder $containerBuilder;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|Definition
     */
    private Definition $preprocessorMiddlewareDefinition;

    protected function setUp(): void
    {
        $this->containerBuilder = $this->createMock(ContainerBuilder::class);
        $this->preprocessorMiddlewareDefinition = $this->createMock(Definition::class);
    }

    protected function tearDown(): void
    {
        unset($this->containerBuilder, $this->preprocessorMiddlewareDefinition);
    }

    public function testReturnIfNoPreprocessorMiddleware(): void
    {
        $this->containerBuilder
            ->expects(static::once())
            ->method('has')
            ->willReturn(false);

        $this->containerBuilder
            ->expects(static::never())
            ->method('getDefinition');

        $compilerPass = new MessengerPreprocessorPass();
        $compilerPass->process($this->containerBuilder);

    }

    public function testLoadPreprocessors(): void
    {
        $preprocessors = [
            'first' => null,
            'second' => null,
        ];

        $this->containerBuilder
            ->expects(static::once())
            ->method('has')
            ->willReturn(true);

        $this->containerBuilder
            ->expects(static::once())
            ->method('getDefinition')
            ->with(PreprocessorMiddleware::class)
            ->willReturn($this->preprocessorMiddlewareDefinition);

        $this->containerBuilder
            ->expects(static::once())
            ->method('findTaggedServiceIds')
            ->with('app.messenger.preprocessor')
            ->willReturn($preprocessors);

        $this->preprocessorMiddlewareDefinition
            ->expects(static::exactly(2))
            ->method('addMethodCall')
            ->with(
                'addPreprocessor',
                static::callback(static function (array $references) use (&$preprocessors) {
                    $key = key($preprocessors);
                    next($preprocessors);

                    return count($references) === 1 && (string) current($references) === $key;
                })
            );

        $compilerPass = new MessengerPreprocessorPass();
        $compilerPass->process($this->containerBuilder);
    }
}
