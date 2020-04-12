<?php
/**
 *
 * @package     om
 * @author      Jayanka Ghosh
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        https://github.com/jayankaghosh/
 */

namespace Om\DependencyInjection\Resolvers;

use Om\DiConfig\Config;
use Om\ObjectManager\ObjectManager;
use Om\Registry\Registry;

class PluginResolver
{

    private $pluginList = null;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * PluginResolver constructor.
     */
    public function __construct()
    {
        $this->config = Registry::get(Registry::DI_CONFIG_KEY);
        $this->objectManager = ObjectManager::getInstance();
    }

    /**
     * @param $classInstance
     * @param string $method
     * @param array $arguments
     * @return array
     */
    public function resolveBefore($classInstance, $method, $arguments)
    {
        $involvedClasses = class_parents($classInstance);
        $method = 'before' . ucfirst($method);
        foreach ($involvedClasses as $class) {
            $plugins = $this->getPluginList($class);
            foreach ($plugins as $plugin) {
                if (method_exists($plugin, $method)) {
                    $pluginInstance = $this->objectManager->get($plugin);
                    $arguments = $pluginInstance->{$method}($classInstance, ...$arguments);
                }
            }
        }
        return $arguments;
    }

    /**
     * @param $classInstance
     * @param string $method
     * @param callable $proceed
     * @param array $arguments
     * @return array
     */
    public function resolveAround($classInstance, $method, $proceed, $arguments)
    {
        $involvedClasses = class_parents($classInstance);
        $method = 'around' . ucfirst($method);
        $result = null;
        $aroundExecuted = false;
        foreach ($involvedClasses as $class) {
            $plugins = $this->getPluginList($class);
            foreach ($plugins as $plugin) {
                if (method_exists($plugin, $method)) {
                    $pluginInstance = $this->objectManager->get($plugin);
                    $result = $pluginInstance->{$method}($classInstance, $proceed, ...$arguments);
                    $aroundExecuted = true;
                }
            }
        }
        if (!$aroundExecuted) {
            $result = $proceed(...$arguments);
        }
        return $result;
    }

    /**
     * @param $classInstance
     * @param string $method
     * @param mixed $result
     * @return array
     */
    public function resolveAfter($classInstance, $method, $result)
    {
        $involvedClasses = class_parents($classInstance);
        $method = 'after' . ucfirst($method);
        foreach ($involvedClasses as $class) {
            $plugins = $this->getPluginList($class);
            foreach ($plugins as $plugin) {
                if (method_exists($plugin, $method)) {
                    $pluginInstance = $this->objectManager->get($plugin);
                    $result = $pluginInstance->{$method}($classInstance, $result);
                }
            }
        }
        return $result;
    }

    /**
     * @param string $pluginType
     * @return array
     */
    protected function getPluginList($pluginType): array
    {
        if (!$this->pluginList) {
            $plugins = [];
            foreach ($this->config->getTypes() as $typeName => $type) {
                $pluginList = $type->getPlugins();
                foreach ($pluginList as $plugin) {
                    if (!$plugin->isDisabled()) {
                        $plugins[$typeName][$plugin->getName()] = $plugin->getType();
                    }
                }
            }
            $this->pluginList = $plugins;
        }
        return $this->pluginList[$pluginType] ?? [];
    }
}