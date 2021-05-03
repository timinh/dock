<?php

namespace Timinh\Commands;

use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class AddMakefileCommand extends BaseCommand
{
    protected static $defaultName = 'add:makefile';
    protected $description = 'Ajout d\'un makefile dans le projet';
    protected $help = 'Ajoute un fichier Makefile dans le projet avec des commandes utiles';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->selectDockerOrNot($input, $output);
        return 1;
    }

    public function selectDockerOrNot(InputInterface $input, OutputInterface $output)
    {
        $useDocker              = false;
        $dockerContainer        = '';
        $useSymfony             = false;
        $useNode                = false;
        $nodeContainer          = '';
        $useElasticsearch       = false;
        $elasticsearchContainer = '';

        $helper = $this->getHelper('question');

        // utilisation docker
        $dcInFolder = file_exists('./docker-compose.yml');
        $question = new ConfirmationQuestion('Souhaitez-vous utiliser docker ? (Y/n)', true);
        if ($dcInFolder) {
            $useDocker = true;
            $output->writeln('Le fichier \'docker-compose.yml\' a été détecté dans le dossier.');
        } else {
            $useDocker = $helper->ask($input, $output, $question);
        }
        if ($useDocker) {
            $question = new Question('Quel est le nom du container de votre application ? (app)', 'app');
            $dockerContainer = $helper->ask($input, $output, $question);
        }

        // projet symfony
        $question = new ConfirmationQuestion('Souhaitez-vous ajouter les commandes pour Symfony ? (Y/n)', true);
        $useSymfony = $helper->ask($input, $output, $question);

        // utilisation nodejs
        $question = new ConfirmationQuestion('Souhaitez-vous ajouter les commandes pour NodeJs ? (Y/n)', true);
        $useNode = $helper->ask($input, $output, $question);
        if ($useNode && $useDocker) {
            $question = new Question('Quel est le nom du container pour nodejs ? (front)', 'front');
            $nodeContainer = $helper->ask($input, $output, $question);
        }

        // utilisation elasticsearch
        $question = new ConfirmationQuestion('Souhaitez-vous ajouter les commandes pour Elasticsearch ? (y/N)', false);
        $useElasticsearch = $helper->ask($input, $output, $question);
        if ($useElasticsearch) {
            if ($useSymfony) {
                $output->writeln('La configuration Elasticsearch sera lue dans votre fichier .env.local, pensez à ajouter la variable ELASTICSEARCH_HOST.');
            } else {
                $question = new Question('Quel est le nom du serveur(ou conteneur) pour elasticsearch ? (localhost:9200)', 'localhost:9200');
                $elasticsearchContainer = $helper->ask($input, $output, $question);
            }
        }
        // génération Makefile
        $this->service->generateMakefile($useDocker, $dockerContainer, $useSymfony, $useNode, $nodeContainer, $useElasticsearch, $elasticsearchContainer);
    }
}
