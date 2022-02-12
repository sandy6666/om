<?php
declare(strict_types=1);

namespace Om\ObjectManager;

use Om\DependencyInjection\TypeGenerator;
use Om\Exception\OmException;
use Om\ObjectManager\ObjectManager;

class ReflectionClass
{
    /**
     * @var TypeGenerator
     */
    private $typeGenerator;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * ReflectionClass constructor.
     * @param ObjectManager $objectManager
     */
    public function __construct(
        ObjectManager $objectManager
    )
    {
        $this->typeGenerator = new TypeGenerator();
        $this->objectManager = $objectManager;
    }

    /**
     * @param $class
     * @param array $arguments
     * @return array
     * @throws OmException
     */
    public function parseClass($class, $arguments = [])
    {
        try {
            $constructorArguments = [];
            $class = new \ReflectionClass($class);
            $constructor = $class->getConstructor();
            if ($constructor && count($constructor->getParameters())) {
                foreach ($constructor->getParameters() as $key => $constructorParameter) {
                    $this->parseConstructorParameter($key, $constructorParameter, $arguments, $constructorArguments);
                }
            }
            return $constructorArguments;
        } catch (\Exception $exception) {
            throw new OmException($exception->getMessage());
        }
    }

    /**
     * @param int $key
     * @param \ReflectionParameter $constructorParameter
     * @param array $arguments
     * @param array $constructorArguments
     * @throws OmException
     */
    private function parseConstructorParameter($key, $constructorParameter, $arguments, &$constructorArguments)
    {
        if (array_key_exists($constructorParameter->getName(), $arguments)) {
            $constructorArguments[$key] = $arguments[$constructorParameter->getName()];
        } else if ($constructorParameter->isOptional()) {
            $constructorArguments[$key] = $constructorParameter->getDefaultValue();
        } else {
            try {
                $constructorParameterClass = $constructorParameter->getClass();
            } catch (\Exception $exception) {
                $this->typeGenerator->generateClassIfPossible($constructorParameter->getType()->getName());
                $constructorParameterClass = $constructorParameter->getClass();
            }
            if ($constructorParameterClass) {
                $constructorParameterName = $constructorParameterClass->getName();
                $object = $this->objectManager->get($constructorParameterName);
                $constructorArguments[$key] = $object;
            }
        }
    }
}
