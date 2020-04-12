<?php
/**
 *
 * @package     om
 * @author      Jayanka Ghosh
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        https://github.com/jayankaghosh/
 */

namespace Om\DependencyInjection\Resolvers\TypeResolver;

use Om\DiConfig\Config\Type\Argument;
use Om\DiConfig\Config\Type\Argument\Item;
use Om\Exception\OmException;

class ArrayResolver implements TypeResolverInterface
{
    /**
     * @var TypeResolverInterface[]
     */
    private $argumentTypeResolvers;

    /**
     * ArrayResolver constructor.
     * @param TypeResolverInterface[] $argumentTypeResolvers
     */
    public function __construct(
        array $argumentTypeResolvers
    )
    {
        $this->argumentTypeResolvers = $argumentTypeResolvers;
    }

    /**
     * @param string $type
     * @param array $value
     * @return mixed
     * @throws OmException
     */
    public function resolve($type, $value)
    {
        return $this->formatArgumentByType($type, $value);
    }

    /**
     * @param $type
     * @param Item[] $value
     * @return array|mixed
     * @throws OmException
     */
    protected function formatArgumentByType($type, $value)
    {
        if (array_key_exists($type, $this->argumentTypeResolvers)) {
            return $this->argumentTypeResolvers[$type]->resolve($type, $value);
        } else if ($type === Argument::TYPE_ARRAY) {
            $response = [];
            if (!is_array($value)) {
                throw new OmException('value is not of type "array"');
            }
            foreach ($value as $item) {
                $type = $item->getType();
                $value = $item->getValue();
                $response[$item->getName()] = $this->formatArgumentByType($type, $value);
            }
            return $response;
        } else {
            throw new OmException(sprintf('type="%s" not supported', $type));
        }
    }
}