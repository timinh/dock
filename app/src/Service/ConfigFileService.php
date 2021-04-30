<?php

namespace Timinh\Service;

use stdClass;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class ConfigFileService
{
    protected $project_path;
    protected $outputFile;

    public function __construct(string $path)
    {
        $this->project_path = $path;
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
     * @Return string : Le nom du fichier à générer
     */
    public function getOutputPath(): string
    {
        return $this->project_path . '/' . $this->outputFile;
    }

    /**
     * @Return array : La liste des templates disponibles (dossiers)
     */
    public function getTemplates(string $folder): array
    {
        $templates = [];
        if ($handle = opendir(dirname(__DIR__) . '/Templates/' . $folder . '/')) {
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
    public function getVersions(string $template): array
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
     * @Return string : Le contenu d'un template
     */
    public function getTemplateContent(string $template): string
    {
        return file_get_contents(dirname(__DIR__) . '/Templates/' . $template);
    }

    /**
     * @Return void: Ecrit un fichier yaml avec le contenu donné
     */
    public function writeFile(string $content, int $mode = 0)
    {
        if ($this->getOutputPath()) {
            file_put_contents($this->getOutputPath(), $content, $mode);
        }
    }
}
