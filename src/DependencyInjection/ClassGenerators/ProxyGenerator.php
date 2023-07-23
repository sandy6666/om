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
use Om\Code\Generators\Expr;
use Om\DependencyInjection\NonInterceptableInterface;
use Om\DependencyInjection\ProxyRendererFactory;

class ProxyGenerator extends AbstractClassGenerator
{

    /**
     * @param ClassGenerator $classGenerator
     * @param string $className
     * @return ClassGenerator
     * @throws \ReflectionException
     */
    protected function getClass(ClassGenerator $classGenerator, $className): ClassGenerator
    {
        $classGenerator
            ->setName($className."\\Proxy")
            ->setParent('\\' . $className)
            ->addImplements('\\' . NonInterceptableInterface::class)
            ->addComment("This class is a middleware between the \\$className implementation and caller");

        $classReflection = new \ReflectionClass($className);
        $classMethods = $classReflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        $classGenerator->addVariable('proxyRenderer', 'private');
        $classGenerator->addMethod(
            '__construct',
            [
                [
                    'type' => '\\' . ProxyRendererFactory::class,
                    'name' => 'proxyRendererFactory'
                ]
            ],
            sprintf('$this->proxyRenderer = $proxyRendererFactory->create([\'instanceName\' => \\%s::class]);', $className),
            null
        );

        if ($classMethods) {
            foreach ($classMethods as $classMethod) {
                if (
                    $classMethod->isConstructor() ||
                    $classMethod->isStatic() ||
                    $classMethod->isAbstract()
                ) {
                    continue;
                }
                $methodName = $classMethod->getName();
                $methodArguments = [];
                if ($classMethod->getReturnType()) {
                    $returnType = $classMethod->getReturnType()->getName();
                    if (!$classMethod->getReturnType()->isBuiltin() && $returnType !== 'self') {
                        $returnType = '\\' . $returnType;
                    }
                    $methodReturnType = [
                        'type' => $returnType,
                        'nullable' => $classMethod->getReturnType()->allowsNull()
                    ];
                } else {
                    $methodReturnType = ClassGenerator::RETURN_TYPE_NONE;
                }
                foreach ($classMethod->getParameters() as $parameter) {
                    $methodArgument = [
                        'type' => $parameter->getType() ? $parameter->getType()->getName() : null,
                        'name' => $parameter->getName()
                    ];

                    if ($parameter->getType()) {
                        $methodArgument['type'] = '\\' . $methodArgument['type'];
                    }

                    if ($parameter->isPassedByReference()) {
                        $methodArgument['call_by_reference'] = true;
                    }

                    if ($parameter->allowsNull()) {
                        $methodArgument['nullable'] = true;
                    }
                    try {
                        if ($parameter->isOptional()) {
                            $methodArgument['default'] = $parameter->getDefaultValue();
                            if ( $parameter->isDefaultValueConstant()) {
                                $defaultConstant = $parameter->getDefaultValueConstantName();
                                $defaultConstant = ltrim($defaultConstant, '\\');
                                if (!stristr($defaultConstant, 'self::') && !stristr($defaultConstant, 'parent::')) {
                                    $defaultConstant = '\\' . $defaultConstant;
                                }
                                $methodArgument['default'] = new Expr($defaultConstant);
                            }
                        }
                    } catch (\Exception $exception) {}
                    $methodArguments[$parameter->getName()] = $methodArgument;
                }

                $methodArgumentsInBody = [];
                foreach (array_keys($methodArguments) as $argument) {
                    $methodArgumentsInBody[] = '$' . $argument;
                }
                $methodArgumentsInBody = implode(', ', $methodArgumentsInBody);

                if ($classMethod->getReturnType() && $classMethod->getReturnType()->getName() === 'void') {
                    $returnStatement = '';
                } else {
                    $returnStatement = 'return ';
                }

                $classGenerator->addMethod(
                    $methodName,
                    $methodArguments,
                    sprintf('%s$this->proxyRenderer->callParentFunction(\'%s\', func_get_args());', $returnStatement, $methodName),
                    $methodReturnType,
                    'public',
                    [
                        "@inheritdoc"
                    ]
                );
            }
        }

        return $classGenerator;
    }

    protected function getClassName($className)
    {
        return str_replace("\\", "/", $className)."/Proxy.php";
    }
}
