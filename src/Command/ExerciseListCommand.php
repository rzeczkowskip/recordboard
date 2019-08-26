<?php

namespace App\Command;

use App\Repository\ExerciseRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ExerciseListCommand extends Command
{
    protected static $defaultName = 'app:exercise:list';

    private ExerciseRepository $exerciseRepository;

    public function __construct(ExerciseRepository $exerciseRepository)
    {
        $this->exerciseRepository = $exerciseRepository;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('List exercises')
            ->addOption(
                'name',
                null,
                InputOption::VALUE_OPTIONAL,
                'Filters exercises by name',
            )
            ->addOption(
                'attribute',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'Filters exercises by attributes'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $exercises = $this->exerciseRepository->getExercisesList();

        $table = new Table($output);
        $table->setHeaderTitle(sprintf(
            'Exercises (%s)',
            \count($exercises),
        ));

        $table->setHeaders([
            'Name',
            'Attributes',
            'ID',
        ]);

        foreach ($exercises as $exercise) {
            $table->addRow([
                $exercise->name,
                implode(', ', $exercise->attributes),
                $exercise->id->toString(),
            ]);
        }

        $table->render();
    }
}