<?php

$pharFile = 'dist/dock.phar';

if (file_exists($pharFile)) {
    unlink($pharFile);
}
if (file_exists($pharFile . '.gz')) {
    unlink($pharFile . '.gz');
}

$p = new Phar($pharFile);

$p->buildFromDirectory('app/');

$p->setDefaultStub('index.php', '/index.php');

$p->compress(Phar::GZ);
echo "Le fichier $pharFile a bien été créé";
