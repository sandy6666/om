<?php

namespace Om\Console;

error_reporting(E_ALL);
ini_set('display_errors', true);

require_once __DIR__ . '/../../vendor/autoload.php';

use ReflectionClass;

$objectManagerFactory = new \Om\OmFactory();
$objectManager = $objectManagerFactory->getInstance();


class RegisterCliCommands
{
    private $writableDir = __DIR__. '/../../var/cache/';

    private $commandsListFile = 'commands.json';

    public function __construct()
    {
    }

    public function getCommandsList()
    {
        $cliCommandsClasses = [];
        $cliCommandsClasses[] = \Om\Console\TestCliCommand::class;
        $commandsJsonFileData = [];
        foreach ($cliCommandsClasses as $cliCommandsClass) {
            $reflectionClass = new ReflectionClass($cliCommandsClass);
            $registeredCommand = $reflectionClass->getProperty('command');
            $registeredCommand->setAccessible(true);
            $command = $registeredCommand->getDefaultValue();
            $commandsJsonFileData[$command] = [
                'namespace' => $cliCommandsClass
            ];
        }
        $commandsJsonFileData = json_encode($commandsJsonFileData);
        if(!file_exists($this->writableDir . $this->commandsListFile)) {
            mkdir($this->writableDir, 0777, true);
        }
        $commandsJsonFileStream = fopen($this->writableDir . $this->commandsListFile, 'w');
        fwrite($commandsJsonFileStream, $commandsJsonFileData);
    }
}


$registerCliCommandObject = $objectManager->get(RegisterCliCommands::class);
$registerCliCommandObject->getCommandsList();