<?php

namespace Timinh\Commands;

use Symfony\Component\Console\Command\Command;
use Timinh\Service\DockerService;

class BaseContainerCommand extends Command
{
    protected $dockerService;
    protected $description = '';
    protected $help = '';

    public function __construct()
    {
        $this->dockerService = new DockerService(getcwd());
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription($this->description)
            ->setHelp($this->help);
    }
}
