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
use Om\Registry\Registry;
use Om\Tools\File;

abstract class AbstractClassGenerator
{
    /**
     * @var ClassGenerator
     */
    protected $classGenerator;
    /**
     * @var File
     */
    protected $file;

    /**
     * FactoryGenerator constructor.
     */
    public function __construct()
    {
        $this->classGenerator = new ClassGenerator();
        $this->file = new File();
    }

    /**
     * @param ClassGenerator $classGenerator
     * @param string $className
     * @return ClassGenerator
     */
    abstract protected function getClass(ClassGenerator $classGenerator, $className): ClassGenerator;

    /**
     * @param string $className
     * @return string
     */
    abstract protected function getClassName($className);

    /**
     * @param $className
     */
    public function generate($className)
    {
        $class = $this->getClass($this->classGenerator, $className);
        $className = $this->getClassName($className);
        $generatedDirectoryPath = Registry::get(Registry::GENERATED_DIRECTORY_PATH_KEY);
        $classPath = $generatedDirectoryPath . DIRECTORY_SEPARATOR .$className;
        $this->file->load($classPath)->write($class);
        require $classPath; // require the file so the class can be autoloaded
    }
}
