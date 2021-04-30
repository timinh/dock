<?php

namespace Timinh\Service;

class MakefileService extends ConfigFileService implements IService
{
    public function __construct(string $path)
    {
        parent::__construct($path);
        $this->outputFile = 'Makefile';
    }

    /**
     * @Return array : La liste des templates disponibles (dossiers)
     */
    public function getConfigTemplates(): array
    {
        return parent::getTemplates('Makefiles');
    }

    /**
     * @Return array : La liste des versions disponible pour un template
     */
    public function getConfigVersions(string $template): array
    {
        return parent::getVersions('Makefiles/' . $template);
    }

    /**
     * @Return bool : génère le fichier Makefile
     */
    public function generateMakefile(bool $useDocker = true, string $dockerContainer = 'app', bool $useSymfony = true, bool $useNode = true): bool
    {
        $contentFile = $useDocker ? 'avec_docker.txt' : 'sans_docker.txt';
        $content = parent::getTemplateContent('Makefiles/' . $contentFile);
        if ($useSymfony) {
            $sfContent = parent::getTemplateContent('Makefiles/symfony.txt');
            $content  = str_replace('{{symfony_commands}}', $sfContent, $content);
        }
        if ($useNode) {
            $nodeContent = parent::getTemplateContent('Makefiles/node.txt');
            $content  = str_replace('{{node_commands}}', $nodeContent, $content);
        }
        parent::writeFile(str_replace('{{container_name}}', $dockerContainer, $content));
        return true;
    }
}
