<?php

namespace Om\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

class CliCommand
{
    private Application $application;

    protected String $name;

    protected array $arguments;

    protected Command $cliCommand;

    protected array $options;

    protected $help;

    protected string $command;

    /**
     * @param Application $application
     */
    public function __construct(
        Application $application
    ) {
        $this->application = $application;
    }

    /**
     * @return void
     */
    protected function registerCliCommand()
    {
        $this->cliCommand = $this->application->register($this->command);
        $this->cliCommand->setName($this->name);
        foreach ($this->arguments as $argument) {
            $this->cliCommand->addArgument(
                $argument['name'],
                    $argument['type'] ?? InputArgument::OPTIONAL,
                    $argument['description'] ?? ""
            );
        }
        foreach ($this->options as $option) {
            $this->cliCommand->addOption(
                $option['name'],
                    $option['shortcut'] ?? null,
                    $option['type'] ?? InputOption::VALUE_OPTIONAL
            );
        }

        $this->cliCommand->setHelp($this->help ?? "");
    }

    /**
     * @param callable $callableFunction
     * @return Command
     * @throws ExceptionInterface
     */
    protected function execute(callable $callableFunction): Command
    {
        return $this->cliCommand->setCode($callableFunction);
    }
}