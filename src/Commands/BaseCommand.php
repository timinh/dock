<?php

namespace Timinh\Commands;

use Symfony\Component\Console\Command\Command;

class BaseCommand extends Command
{
    protected $service;
    protected $description = '';
    protected $help = '';

    public function __construct(string $iService)
    {
        $this->service = new $iService(getcwd());
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription($this->description)
            ->setHelp($this->help);
    }
}
