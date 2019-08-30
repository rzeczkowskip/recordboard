<?php
namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ExerciseListCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:exercise:list');

        $tester = new CommandTester($command);

        $tester->execute([
            'command'  => $command->getName(),
        ]);

        $output = $tester->getDisplay();
        static::assertStringContainsString('Exercises (1)', $output);
        static::assertStringContainsString('Deadlift', $output);

        self::ensureKernelShutdown();
    }
}
