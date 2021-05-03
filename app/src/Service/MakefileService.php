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
    public function generateMakefile(bool $useDocker = true, string $dockerContainer = 'app', bool $useSymfony = true, bool $useNode = true, string $nodeContainer, bool $useElasticsearch = false, string $elasticsearchContainer): bool
    {
        $contentFile = $useDocker ? 'avec_docker.txt' : 'sans_docker.txt';
        $content = parent::getTemplateContent('Makefiles/' . $contentFile);

        // installation par défaut des commandes composer
        $composerContent = parent::getTemplateContent('Makefiles/composer.txt');
        $content = str_replace('{{composer_commands}}', $composerContent, $content);

        $sfContent = $useSymfony ? parent::getTemplateContent('Makefiles/symfony.txt') : '';
        $sfEnv     = $useSymfony ? "APP_ENV := $$(grep APP_ENV= .env.local | cut -d '=' -f2-)" : "";
        $content  = str_replace('{{symfony_commands}}', $sfContent, $content);

        $nodeContent = $useNode ? parent::getTemplateContent('Makefiles/node.txt') : '';
        $content  = str_replace('{{node_commands}}', $nodeContent, $content);

        if ($useElasticsearch) {
            $elasticsearchContainer = $useSymfony ? "ELASTICSEARCH_HOST := $$(grep ELASTICSEARCH_HOST .env.local | cut -d '=' -f2-)" : "ELASTICSEARCH_HOST := " . $elasticsearchContainer;
            $elasticsearchContent = parent::getTemplateContent('Makefiles/elasticsearch.txt');
            $content = str_replace('{{elasticsearch_commands}}', $elasticsearchContent, $content);
        } else {
            $content = str_replace('{{elasticsearch_commands}}', '', $content);
        }
        parent::writeFile(str_replace(['{{php_container_name}}', '{{node_container_name}}', '{{elasticsearch_server}}', '{{symfony_env}}'], [$dockerContainer, $nodeContainer, $elasticsearchContainer, $sfEnv], $content));
        return true;
    }
}
