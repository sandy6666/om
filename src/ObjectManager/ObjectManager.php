<?php
declare(strict_types=1);

namespace Om\ObjectManager;


use Om\DependencyInjection\Resolvers\TypeResolver;
use Om\Exception\OmException;
use Om\Registry\Registry;
use Om\Tools\File;

class ObjectManager
{
    /**
     * @var ReflectionClass
     */
    private $reflectionClass = null;

    /**
     * @var TypeResolver
     */
    private $typeResolver = null;
    /**
     * @var File
     */
    private $file;

    /**
     * ObjectManager constructor.
     * @param TypeResolver $typeResolver
     */
    public function __construct(
        TypeResolver $typeResolver
    )
    {
        $this->reflectionClass = new ReflectionClass($this);
        $this->typeResolver = $typeResolver;
        $this->file = new File();
    }

    /**
     * @return $this
     */
    public static function getInstance()
    {
        return Registry::get(Registry::OBJECT_MANAGER_KEY);
    }

    /**
     * @param $class
     * @param array $arguments
     * @return mixed|null
     * @throws OmException
     */
    public function get($class, $arguments = []) {
        $key = Registry::OBJECT_BUCKET_KEY_PREFIX.strtolower(str_replace("\\", "_", $class));
        if (!Registry::get($key)) {
            $obj = self::create($class, $arguments);
            Registry::set($key, $obj);
        }
        return Registry::get($key);
    }

    /**
     * @param $class
     * @param array $arguments
     * @return mixed
     * @throws OmException
     */
    public function create($class, $arguments = []) {
        $arguments = $this->typeResolver->resolveArguments($class, $arguments);
        $class = $this->typeResolver->resolve($class);
        $arguments = $this->reflectionClass->parseClass($class, $arguments);
        return new $class(...$arguments);
    }

    public function __destruct()
    {
        $canGenerate = Registry::get(Registry::CAN_GENERATE_CLASS_KEY);
        if ($canGenerate) {
            $generatedDirectoryPath = Registry::get(Registry::GENERATED_DIRECTORY_PATH_KEY);
//            $this->file->deleteDirectory($generatedDirectoryPath);
        }
    }
}