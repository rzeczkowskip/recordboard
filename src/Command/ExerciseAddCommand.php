<?php

namespace App\Command;

use App\Entity\Exercise;
use App\Message\Exercise\CreateExercise;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\ValidationStamp;

class ExerciseAddCommand extends Command
{

    protected static $defaultName = 'app:exercise:add';

    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Add new exercise')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Name of an exercise'
            )
            ->addOption(
                'attribute',
                'a',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'Exercise attributes. You can leave this field empty to select exercise from list' .
                'Allowed values: '.implode(',', Exercise::getAllowedAttributes()).'.',
                []
            )
        ;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $attributes = $input->getOption('attribute');
        if (!$attributes) {
            $io = new SymfonyStyle($input, $output);

            $choices = array_values(Exercise::getAllowedAttributes());
            $question = new ChoiceQuestion(
                'Select attributes of the exercise',
                $choices
            );

            $question->setMultiselect(true);
            $question->setAutocompleterValues($choices);

            $attributes = $io->askQuestion($question);
            $input->setOption('attribute', $attributes);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $message = CreateExercise::withData(
            $input->getArgument('name'),
            $input->getOption('attribute'),
        );

        $this->messageBus->dispatch(
            $message,
            [new ValidationStamp([])]
        );

        $io->success('Exercise created');
    }
}
