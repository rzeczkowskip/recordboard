<?php
namespace App\Tests\Command;

use App\Command\ExerciseAddCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Messenger\Exception\ValidationFailedException;

class ExerciseAddCommandTest extends KernelTestCase
{
    private ExerciseAddCommand $command;

    protected function setUp(): void
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $this->command = $application->find('app:exercise:add');
    }

    protected function tearDown(): void
    {
        unset($this->command);
    }

    public function testExecute(): void
    {
        $tester = new CommandTester($this->command);

        $tester->setInputs(['weight']);

        $tester->execute([
            'command'  => $this->command->getName(),
            'name' => 'Test exercise',
        ]);

        $output = $tester->getDisplay();
        static::assertStringContainsString('[OK] Exercise created', $output);
    }

    public function testExecuteValidationErrors(): void
    {
        $tester = new CommandTester($this->command);

        $this->expectException(ValidationFailedException::class);

        $tester->execute([
            'command'  => $this->command->getName(),
            'name' => 'Test exercise',
            '--attribute' => ['invalid'],
        ]);
    }
}
