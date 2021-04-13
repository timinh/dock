<?php

namespace Timinh\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitDockerComposeCommand extends BaseContainerCommand
{
    protected static $defaultName = 'init';
    protected $description = 'Initialisation du projet docker';
    protected $help = 'Création du docker-compose.yml';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->dockerService->createDockerComposeFile()) {
            $output->write('Le fichier a été créé,');
        } else {
            $output->write('le fichier existe déja,');
        }
        $output->writeln(' vous pouvez utiliser la commande \'add\' pour ajouter des containers.');
        return 1;
    }
}
