<?php

namespace Timinh\Service;

use stdClass;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class GitlabService extends ConfigFileService implements IService
{

    public function __construct(string $path)
    {
        parent::__construct($path);
        $this->outputFile = '.gitlab-ci.yml';
    }

    /**
     * @Return array : La liste des templates disponibles (dossiers)
     */
    public function getConfigTemplates(): array
    {
        return parent::getTemplates('Gitlab');
    }

    /**
     * @Return array : La liste des versions disponible pour un template
     */
    public function getConfigVersions(string $template): array
    {
        return parent::getVersions('Gitlab/' . $template);
    }

    /**
     * @Return void : génère le fichier .gitlab-ci.yml
     */
    public function generateCi(string $template): bool
    {
        $start = Yaml::parse(parent::getTemplateContent('Gitlab/' . $template));
        $end = Yaml::parse(parent::getTemplateContent('Gitlab/stages.yml'));
        parent::writeFile(
            Yaml::dump(
                array_merge($start, $end),
                Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK
            )
        );
        return true;
    }
}
