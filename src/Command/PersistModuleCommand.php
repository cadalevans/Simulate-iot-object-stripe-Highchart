<?php

namespace App\Command;

use App\Entity\Data;
use App\Service\PredictionService;
use App\Repository\ModuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:persist-module',
    description: 'Add a short description for your command',
    aliases: ['app:add-module'],
    hidden: false
)]
class PersistModuleCommand extends Command
{
    // Add event dispatcher

    private $entityManager;
    private $moduleRepository;
    private $PredictionService;

    public function __construct(
        EntityManagerInterface $entityManager,
        ModuleRepository $moduleRepository,
        PredictionService $predictionService

    ) {
        $this->entityManager = $entityManager;
        $this->moduleRepository = $moduleRepository;
        $this->PredictionService = $predictionService;

        parent::__construct();
    }

    // Other methods...

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            // Generate and persist random data
            $this->generateAndPersistRandomModule();

            $output->writeln('Random is_Operating module persisted successfully.');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('An error occurred: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }

    private function generateAndPersistRandomModule(): void
    {
        $modules = $this->moduleRepository->findAll();

        foreach ($modules as $module) {
            // Get the previous state
            $previousState = $module->isActual();

            // Set random isOperating value
            $isOperating = (bool) rand(0, 1);
            $module->setIsOperating($isOperating);

            // Check if the module was fixed
            if (!$previousState && $isOperating) {
                $module->incrementFixCount();
            }
            // Now we are going to set the prediction
            $prediction = $this->PredictionService->predictSellOrConserve(
                $module->getBuyingDate(),
                $module->isIsNew(),
                $module->getFixCount()
            );
            $module->setPrediction($prediction);
            // Update the actual state
            $module->setActual($isOperating);

            // If module is not operating, create and persist new data entity with values 0
            // so that in my chart the line can downgrade the user can see the zero value if a module is not workink based to is
            if (!$isOperating) {
                $data = new Data();
                $data->setMeasuredValue(0);
                $data->setTemperature(0);
                $data->setSpeed(0);
                $data->setWorkingTime(new \DateTime()); // Set appropriate date
                $data->setModule($module);

                $this->entityManager->persist($data);

            }
            $this->entityManager->persist($module);

        }

        // Flush changes
        $this->entityManager->flush();
    }
}
// command : php bin/console app:persist-module
//linux link : https://www.brainvire.com/configure-cron-jobs-symfony/
// on window we can see it on stack overflow : https://stackoverflow.com/questions/71352951/how-to-run-a-symfony-command-in-windows-task-scheduler
