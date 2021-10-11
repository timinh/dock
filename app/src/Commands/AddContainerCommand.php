<?php

namespace Timinh\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class AddContainerCommand extends BaseCommand
{
    protected static $defaultName = 'add:container';
    protected $description = 'Ajout d\'un container à la config docker';
    protected $help = 'Ajoute un container à la config docker';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->selectServiceType($input, $output);
        return 1;
    }

    private function nextChoice(InputInterface $input, OutputInterface $output)
    {
        $services = $this->service->getServices();
        if (count($services) > 0) {
            $services = array_keys($services);
            $output->writeln("Services actuellement dans votre docker-compose.yaml : ");
            foreach ($services as $s) {
                $output->writeln(" - " . $s);
            }
        }
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Souhaitez-vous ajouter un container ? (Y/n)', true);
        if ($helper->ask($input, $output, $question)) {
            $this->selectServiceType($input, $output);
        } else {
            $this->service->writeDockerComposeFile();
            $output->writeln('Le fichier docker-compose.yml a bien été créé.');
        }
        return Command::SUCCESS;
    }

    private function selectServiceType(InputInterface $input, OutputInterface $output)
    {
        $templates = $this->service->getConfigTemplates();
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion('Choisissez le type de container que vous souhaitez : ', $templates, 0);
        $serviceType = $helper->ask($input, $output, $question);
        $this->selectContainerVersion($input, $output, $serviceType);
    }

    private function selectContainerVersion(InputInterface $input, OutputInterface $output, string $serviceType)
    {
        $availableVersions = $this->service->getConfigVersions($serviceType);
        if (count($availableVersions) > 1) {
            $helper = $this->getHelper('question');
            $question = new ChoiceQuestion('Sélectionnez la version du container : ', $availableVersions, 0);
            $selectedVersion = $helper->ask($input, $output, $question);
        } else {
            $selectedVersion = $availableVersions[0];
        }
        // ajout du container
        $this->service->addService($serviceType . '/' . $selectedVersion . '.yml');

        $output->writeln($serviceType . ' a été ajouté à votre projet en version ' . $selectedVersion);
        $this->nextChoice($input, $output);
    }
}
