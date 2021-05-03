<?php
require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Timinh\Service\DockerService;
use Timinh\Service\GitlabService;
use Timinh\Service\MakefileService;

$application = new Application();
$application->add(new \Timinh\Commands\AddContainerCommand(DockerService::class));
$application->add(new \Timinh\Commands\AddGitlabCICommand(GitlabService::class));
$application->add(new \Timinh\Commands\AddMakefileCommand(MakefileService::class));
$application->run();
