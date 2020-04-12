<?php
/**
 *
 * @package     om
 * @author      Jayanka Ghosh
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        https://github.com/jayankaghosh/
 */

namespace Om\DependencyInjection;


use Om\DependencyInjection\ClassGenerators\FactoryGenerator;
use Om\DependencyInjection\ClassGenerators\InterceptorGenerator;
use Om\Registry\Registry;

class TypeGenerator
{
    /**
     * @param $class
     */
    public function generateClassIfPossible($class) {
        $canGenerate = Registry::get(Registry::CAN_GENERATE_CLASS_KEY);
        if ($canGenerate) {
            $this->generateFactory($class);
            $this->generateInterceptor($class);
        }
    }

    /**
     * @param $class
     */
    protected function generateFactory($class)
    {
        $factoryGenerator = new FactoryGenerator();
        $factorySuffix = "Factory";
        if (substr($class, -1*strlen($factorySuffix)) === $factorySuffix) {
            $originalClass = substr($class, 0, -1*strlen($factorySuffix));
            $factoryGenerator->generate($originalClass);
        }
    }

    /**
     * @param $class
     */
    protected function generateInterceptor($class)
    {
        $interceptorGenerator = new InterceptorGenerator();
        $className = explode('\\', $class);
        if ($className[count($className) - 1] === 'Interceptor') {
            array_pop($className);
            $interceptorGenerator->generate(implode('\\', $className));
        }
    }
}