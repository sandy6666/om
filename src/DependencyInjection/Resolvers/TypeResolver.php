<?php
/**
 *
 * @package     om
 * @author      Jayanka Ghosh
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        https://github.com/jayankaghosh/
 */

namespace Om\DependencyInjection\Resolvers;


use Om\DependencyInjection\NonInterceptableInterface;
use Om\DependencyInjection\Resolvers\TypeResolver\ArrayResolver;
use Om\DependencyInjection\Resolvers\TypeResolver\BooleanResolver;
use Om\DependencyInjection\Resolvers\TypeResolver\ComputedResolver;
use Om\DependencyInjection\Resolvers\TypeResolver\ConstResolver;
use Om\DependencyInjection\Resolvers\TypeResolver\NullResolver;
use Om\DependencyInjection\Resolvers\TypeResolver\NumberResolver;
use Om\DependencyInjection\Resolvers\TypeResolver\ObjectResolver;
use Om\DependencyInjection\Resolvers\TypeResolver\StringResolver;
use Om\DependencyInjection\Resolvers\TypeResolver\TypeResolverInterface;
use Om\DependencyInjection\TypeGenerator;
use Om\DiConfig\Config;
use Om\Exception\OmException;
use Om\Registry\Registry;

class TypeResolver implements NonInterceptableInterface
{

    /**
     * @var TypeResolverInterface[]
     */
    protected $argumentTypeResolvers = null;
    /**
     * @var Config
     */
    private $config;

    /**
     * TypeResolver constructor.
     * @param Config $config
     */
    public function __construct(
        Config $config
    )
    {
        $argumentTypeResolvers = [
            Config\Type\Argument::TYPE_BOOLEAN => new BooleanResolver(),
            Config\Type\Argument::TYPE_CONST => new ConstResolver(),
            Config\Type\Argument::TYPE_NULL => new NullResolver(),
            Config\Type\Argument::TYPE_NUMBER => new NumberResolver(),
            Config\Type\Argument::TYPE_OBJECT => new ObjectResolver(),
            Config\Type\Argument::TYPE_STRING => new StringResolver(),
            Config\Type\Argument::TYPE_COMPUTED => new ComputedResolver()
        ];
        $arrayTypeResolver = new ArrayResolver($argumentTypeResolvers);
        $argumentTypeResolvers[Config\Type\Argument::TYPE_ARRAY] = $arrayTypeResolver;
        $this->argumentTypeResolvers = $argumentTypeResolvers;
        $this->config = $config;
    }

    /**
     * @param string $type
     * @return string
     */
    public function resolve($type)
    {
        $preferences = $this->config->getPreferences();
        if (isset($preferences[$type])) {
            $type = $preferences[$type]->getType();
        }

        $canGenerate = Registry::get(Registry::CAN_GENERATE_CLASS_KEY);
        $isInterceptable = !in_array(NonInterceptableInterface::class, class_implements($type));
        if ($canGenerate && $isInterceptable) {
            $interceptorClassName = 'Interceptor';
            $type = $type . '\\' . $interceptorClassName;
        }

        return $type;
    }

    /**
     * @param string $type
     * @param array $arguments
     * @return array
     * @throws OmException
     */
    public function resolveArguments($type, $arguments)
    {
        if (!class_exists($type)) {
            $typeGenerator = new TypeGenerator();
            $typeGenerator->generateClassIfPossible($type);
        }
        $involvedTypes = class_parents($type);
        if (!is_array($involvedTypes)) {
            $involvedTypes = [];
        }
        array_unshift($involvedTypes, $type);
        $types = $this->config->getTypes();
        foreach ($involvedTypes as $type) {
            $type = $types[$type] ?? null;
            if ($type) {
                $injectedArguments = [];
                $argumentList = $type->getArguments();
                foreach ($argumentList as $argument) {
                    $injectedArguments[$argument->getName()] = $this->formatArgumentByType(
                        $argument->getValue(),
                        $argument->getType()
                    );
                }
                $arguments = array_merge($injectedArguments, $arguments);
            }
        }
        return $arguments;
    }

    /**
     * @param string $value
     * @param string $type
     * @return bool|float|mixed|null|string
     * @throws OmException
     */
    protected function formatArgumentByType($value, $type)
    {
        if (array_key_exists($type, $this->argumentTypeResolvers)) {
            return $this->argumentTypeResolvers[$type]->resolve($type, $value);
        } else {
            throw new OmException('type="%s" not supported', [$type]);
        }
    }
}
