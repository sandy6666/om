<?php
/**
 *
 * @package     om
 * @author      Jayanka Ghosh
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        https://github.com/jayankaghosh/
 */

namespace Om\DependencyInjection\ClassGenerators;


use Om\Code\Generators\ClassGenerator;
use Om\DependencyInjection\NonInterceptableInterface;
use Om\ObjectManager\ObjectManager;

class FactoryGenerator extends AbstractClassGenerator
{

    /**
     * @param ClassGenerator $classGenerator
     * @param string $className
     * @return ClassGenerator
     */
    protected function getClass(ClassGenerator $classGenerator, $className): ClassGenerator
    {
        return $classGenerator
            ->setName($className."Factory")
            ->addComment("Use this class to create a new instance of the \\$className class")
            ->addImplements('\\' . NonInterceptableInterface::class)
            ->addMethod("createStatic", [
                [
                    "type" => "array",
                    "name" => "data",
                    "default" => []
                ]
            ], 'return \\'.ObjectManager::class.'::getInstance()->create(\\'.$className.'::class, $data);',
                ClassGenerator::RETURN_TYPE_NONE,
                "public static",
                [
                    "@param array \$data",
                    "@return \\$className"
                ])->addMethod("create", [
                [
                    "type" => "array",
                    "name" => "data",
                    "default" => []
                ]
            ], 'return self::createStatic($data);',
                ClassGenerator::RETURN_TYPE_NONE,
                "public",
                [
                    "@param array \$data",
                    "@return \\$className"
                ]);
    }

    protected function getClassName($className)
    {
        return str_replace("\\", "/", $className)."Factory.php";
    }
}
