<?php
/**
 *
 * @package     om
 * @author      Jayanka Ghosh
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        https://github.com/jayankaghosh/
 */

namespace Om\DependencyInjection\Resolvers\TypeResolver;


use Om\Exception\OmException;
use Om\ObjectManager\ObjectManager;

class ComputedResolver implements TypeResolverInterface
{

    /**
     * @param string $type
     * @param string $value
     * @return mixed
     * @throws OmException
     */
    public function resolve($type, $value)
    {
        $resolverInstance = ObjectManager::getInstance()->get($value);
        if ($resolverInstance instanceof TypeResolverInterface) {
            return $resolverInstance->resolve($type, $value);
        } else {
            throw new OmException('Resolver with type "%s" must implement interface %s', [$type, TypeResolverInterface::class]);
        }
    }
}