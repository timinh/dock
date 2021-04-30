<?php

namespace Timinh\Service;

use Symfony\Component\Yaml\Yaml;

class DockerService extends ConfigFileService implements IService
{
    private $config;
    private $services;
    private $volumes;
    private $networks;

    public function __construct(string $path)
    {
        parent::__construct($path);
        $this->config = [
            'version' => '3.7'
        ];
        $this->outputFile = 'docker-compose.yml';
        $this->services   = [];
        $this->volumes      = [];
        $this->networks     = [];
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
        $template = parent::getTemplateContent('Docker/' . $templateFile);
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
        return parent::getTemplates('Docker');
    }

    /**
     * @Return array : La liste des versions disponible pour un template
     */
    public function getConfigVersions(string $template): array
    {
        return parent::getVersions('Docker/' . $template);
    }

    /**
     * @Return bool : true si le docker-compose a bien été créé
     */
    public function writeDockerComposeFile(): bool
    {
        $services = array_map(function ($arr) {
            return array_pop($arr);
        }, $this->services);
        parent::writeFile(
            Yaml::dump(
                array_merge(
                    $this->getConfig(),
                    ['services' => $services],
                    ['volumes' => $this->getVolumes()]
                ),
                Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK
            )
        );
        parent::writeFile(
            Yaml::dump(
                $this->getNetworks(),
                Yaml::DUMP_OBJECT
            ),
            FILE_APPEND
        );
        return true;
    }
}
