<?php

namespace App\Command;

use App\Entity\Data;
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
    name: 'app:persist-data',
    description: 'Generate and persist random data for modules',
    aliases: ['app:add-data'],
    hidden: false
)]
class PersistDataCommand extends Command
{
    // protected static $defaultName = 'app:persist-data';
    private $entityManager;
    private $moduleRepository;

    public function __construct(EntityManagerInterface $entityManager, ModuleRepository $moduleRepository)
    {
        $this->entityManager = $entityManager;
        $this->moduleRepository = $moduleRepository;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Generate and persist random data for modules');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {

            $this->generateAndPersistRandomData();

            $output->writeln('Random data persisted successfully.');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('An error occurred: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }

    private function generateAndPersistRandomData(): void
    {

        $modules = $this->moduleRepository->findAll();

        // Generate random data and persist it for each module
        foreach ($modules as $module) {
            $isOperating= $module->isIsOperating();
            if (!$isOperating) {
                $data = new Data();
                $data->setMeasuredValue(0);
                $data->setTemperature(0);
                $data->setSpeed(0);
                $data->setWorkingTime(new \DateTime()); // Set appropriate date
                $data->setModule($module);

            }else {
                $data = new Data();
                $data->setMeasuredValue(rand(20, 100));
                $data->setWorkingTime(new \DateTime());
                $data->setSpeed(rand(40, 190));
                $data->setTemperature(rand(30, 100));
                $data->setModule($module);



            }
            $this->entityManager->persist($data);
        }

        $this->entityManager->flush();
    }
}

//Ok i have made all configuration, you just need to execute this command to add random data and
// you can easy configure the schedulled with plannificateur de tache on window but it's very simple on linux; sincerely it have take me some time to test it with task scheduler on window
// command : php bin/console app:persist-data
//linux link : https://www.brainvire.com/configure-cron-jobs-symfony/
// on window we can see it on stack overflow : https://stackoverflow.com/questions/71352951/how-to-run-a-symfony-command-in-windows-task-scheduler but actually doesn't work hope i can find another process

