<?php

namespace Timinh\Service;

use stdClass;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class DockerService
{
    private $config;
    private $project_path;
    private $services;
    private $volumes;
    private $networks;

    public function __construct(string $path)
    {
        $this->config = [
            'version' => '3.7'
        ];
        $this->project_path = $path;
        $this->services   = [];
        $this->volumes      = [];
        $this->networks     = [];
    }

    /**
     * @Return string : Le nom du dossier de travail
     */
    public function getProjectFolder(): string
    {
        return basename($this->project_path);
    }

    /**
     * @Return string : Le chemin complet du dossier de travail
     */
    public function getProjectPath(): string
    {
        return $this->project_path;
    }

    /**
     * @Return string : Le chemin complet du fichier docker-compose
     */
    public function getDockerCompose(): string
    {
        return $this->project_path . '/docker-compose.yml';
    }

    /**
     * @Return bool : 
     */
    public function createDockerComposeFile(): bool
    {
        $dc = $this->getDockerCompose();
        if (!file_exists($dc)) {
            file_put_contents($dc, Yaml::dump($this->config));
            return true;
        }
        return false;
    }

    /**
     * @Return array : config
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @Return array : La liste des containers du projet courant
     */
    public function getServices(): array
    {
        return $this->services;
    }

    /**
     * @Return array : la liste des volumes
     */
    public function getVolumes(): array
    {
        return $this->volumes;
    }

    /**
     * @Return array : la liste des réseaux
     */
    public function getNetworks(): array
    {
        return [
            'networks' => [$this->networks[0] => []]
        ];
    }

    /**
     * @Return bool : true si le container est bien ajouté, false dans le cas contraire
     */
    public function addService(string $templateFile): bool
    {
        $template = file_get_contents(dirname(__DIR__) . '/Templates/' . $templateFile);
        $template = str_replace('PROJECT_FOLDER', $this->getProjectFolder(), $template);
        if (strlen($template) > 0) {
            $servicesArray = Yaml::parse($template);
            $templateName = explode('/', $templateFile)[0];
            $this->services[$templateName] = $servicesArray;

            if (isset($servicesArray[array_keys($servicesArray)[0]]['volumes'][0]) && !in_array($servicesArray[array_keys($servicesArray)[0]]['volumes'][0], $this->volumes)) {
                $tmp = explode(':', $servicesArray[array_keys($servicesArray)[0]]['volumes'][0])[0];
                if ($tmp != './') {
                    $this->volumes[$tmp]['driver'] = 'local';
                }
            }

            if (isset($servicesArray[array_keys($servicesArray)[0]]['networks'][0]) && !in_array($servicesArray[array_keys($servicesArray)[0]]['networks'][0], $this->networks)) {
                $this->networks[] = $servicesArray[array_keys($servicesArray)[0]]['networks'][0];
            }
        }
        return true;
    }

    /**
     * @Return array : La liste des templates disponibles (dossiers)
     */
    public function getConfigTemplates(): array
    {
        $templates = [];
        if ($handle = opendir(dirname(__DIR__) . '/Templates')) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry === '.' || $entry === '..') continue;
                $templates[] = $entry;
            }
            closedir($handle);
        }
        return $templates;
    }

    /**
     * @Return array : La liste des versions disponible pour un template
     */
    public function getConfigVersions(string $template): array
    {
        $versions = [];
        if ($handle = opendir(dirname(__DIR__) . '/Templates/' . $template)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry === '.' || $entry === '..') continue;
                $tmp = explode('.', $entry);
                $tmp = array_splice($tmp, 0, -1);
                $versions[] = implode('.', $tmp);
            }
            closedir($handle);
        }
        return $versions;

        return [];
    }

    /**
     * @Return bool : true si le docker-compose a bien été créé
     */
    public function writeDockerComposeFile(): bool
    {
        $services = array_map(function ($arr) {
            return array_pop($arr);
        }, $this->services);
        $dc = $this->getDockerCompose();
        file_put_contents(
            $dc,
            Yaml::dump(
                array_merge(
                    $this->getConfig(),
                    ['services' => $services],
                    ['volumes' => $this->getVolumes()]
                ),
                Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK
            )
        );
        file_put_contents(
            $dc,
            Yaml::dump(
                $this->getNetworks(),
                Yaml::DUMP_OBJECT
            ),
            FILE_APPEND
        );
        return true;
    }
}
