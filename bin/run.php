<?php

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Javame\UlovDomov\Application;
use Symfony\Component\Yaml\Yaml;

require __DIR__ . '/../vendor/autoload.php';

try {
    $databaseConfiguration = Yaml::parse(file_get_contents(__DIR__ . '/../app/config/database.yml'));
    $application = new Application(
        DriverManager::getConnection(
            $databaseConfiguration['connection'],
            new Configuration()
        )
    );
    $application->run();
} catch (Exception $e) {
    echo sprintf('Exception during application run. Message: %s (%s)', $e->getMessage(), $e->getCode());
    die(1);
}
