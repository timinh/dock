<?php

namespace Timinh\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class AddGitlabCICommand extends BaseCommand
{
    protected static $defaultName = 'add:ci';
    protected $description = 'Ajout d\'une configuration gitlab-ci';
    protected $help = 'Ajoute un fichier .gitlab-ci.yml pour l\'intégration continue';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->selectCIVersion($input, $output, 'gitlab-ci');
        return 1;
    }

    public function selectCIVersion(InputInterface $input, OutputInterface $output, string $serviceType)
    {
        $availableVersions = $this->service->getConfigVersions($serviceType);
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion('Sélectionnez la version de php à utiliser : ', $availableVersions, 0);
        $selectedVersion = $helper->ask($input, $output, $question);

        $this->service->generateCi($serviceType . '/' . $selectedVersion . '.yml');

        $output->writeln('Votre fichier .gitlab-ci.yml a été généré avec ' . $selectedVersion . '.');
    }
}
