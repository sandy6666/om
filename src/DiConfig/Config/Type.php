<?php
/**
 *
 * @package     om
 * @author      Jayanka Ghosh
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        https://github.com/jayankaghosh/
 */

namespace Om\DiConfig\Config;


use Om\DiConfig\Config\Type\Argument;
use Om\DiConfig\Config\Type\Plugin;

class Type
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var array
     */
    private $arguments;

    /**
     * @var
     */
    private $plugins;

    /**
     * Type constructor.
     * @param string $name
     * @param array $arguments
     * @param $plugins
     */
    public function __construct(
        string $name,
        array $arguments,
        $plugins
    )
    {
        $this->setName($name);
        $this->setArguments($arguments);
        $this->setPlugins($plugins);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Argument[]
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @param Argument[] $arguments
     */
    public function setArguments(array $arguments): void
    {
        $this->arguments = [];
        foreach ($arguments as $argument) {
            $this->addArgument($argument);
        }
    }

    /**
     * @param Argument|array $argument
     * @return $this
     */
    public function addArgument($argument)
    {
        if (!$argument instanceof Argument) {
            $argument = new Argument($argument['name'], $argument['type'], $argument['value'] ?? null);
        }
        $this->arguments[$argument->getName()] = $argument;
        return $this;
    }

    /**
     * @return Plugin[]
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * @param mixed $plugins
     */
    public function setPlugins($plugins): void
    {
        $this->plugins = [];
        foreach ($plugins as $plugin) {
            $this->addPlugin($plugin);
        }
    }

    /**
     * @param Plugin|array $plugin
     * @return $this
     */
    public function addPlugin($plugin)
    {
        if (!$plugin instanceof Plugin) {
            $plugin = new Plugin(
                $plugin['name'],
                $plugin['type'],
                isset($plugin['disabled']) ? (bool)$plugin['disabled'] : false
            );
        }
        $this->plugins[$plugin->getName()] = $plugin;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = [
            'name' => $this->getName(),
            'plugins' => [],
            'arguments' => []
        ];
        foreach ($this->getPlugins() as $plugin) {
            $result['plugins'][$plugin->getName()] = $plugin->toArray();
        }
        foreach ($this->getArguments() as $argument) {
            $result['arguments'][$argument->getName()] = $argument->toArray();
        }
        return $result;
    }
}