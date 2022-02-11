<?php
/**
 *
 * @package     om
 * @author      Jayanka Ghosh
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        https://github.com/jayankaghosh/
 */

namespace Om\DependencyInjection;


use Om\DependencyInjection\Resolvers\PluginResolver;

trait InterceptorTrait
{

    private $___pluginResolver = null;

    private function ___getPluginResolver()
    {
        if (!$this->___pluginResolver) {
            $this->___pluginResolver = new PluginResolver();
        }
        return $this->___pluginResolver;
    }

    protected function ___execBefore($arguments, $method)
    {
        $arguments = $this->___getPluginResolver()->resolveBefore($this, $method, $arguments);
        return $arguments;
    }

    protected function ___execAround($arguments, $method)
    {
        $proceed = \Closure::fromCallable(['parent', $method]);
        return $this->___getPluginResolver()->resolveAround($this, $method, $proceed, $arguments);
    }

    protected function ___execAfter($result, $method)
    {
        $result = $this->___getPluginResolver()->resolveAfter($this, $method, $result);
        return $result;
    }
}
