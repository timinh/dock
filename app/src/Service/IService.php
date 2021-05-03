<?php

namespace Timinh\Service;

interface IService
{
    public function getConfigTemplates(): array;
    public function getConfigVersions(string $template): array;
}
