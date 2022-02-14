<?php
/**
 *
 * @package     om
 * @author      Jayanka Ghosh
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        https://github.com/jayankaghosh/
 */

namespace Om\DependencyInjection;


use Om\ObjectManager\ObjectManager;

class ProxyRenderer implements NonInterceptableInterface
{
    private ObjectManager $objectManager;

    private ?string $instanceName = null;

    private $instance = null;

    public function __construct(
        ObjectManager $objectManager,
        string $instanceName
    )
    {
        $this->instanceName = $instanceName;
        $this->objectManager = $objectManager;
    }

    private function getInstance()
    {
        if (!$this->instance) {
            $this->instance = $this->objectManager->get($this->instanceName);
        }
        return $this->instance;
    }

    public function callParentFunction(string $name, array $arguments)
    {
        return call_user_func_array([$this->getInstance(), $name], $arguments);
    }
}
