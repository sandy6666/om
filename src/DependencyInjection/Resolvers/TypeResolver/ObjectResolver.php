<?php
/**
 *
 * @package     om
 * @author      Jayanka Ghosh
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        https://github.com/jayankaghosh/
 */

namespace Om\DependencyInjection\Resolvers\TypeResolver;


use Om\ObjectManager\ObjectManager;
use Om\Exception\OmException;

class ObjectResolver implements TypeResolverInterface
{

    /**
     * @param string $type
     * @param string $value
     * @return mixed
     * @throws OmException
     */
    public function resolve($type, $value)
    {
        return ObjectManager::getInstance()->get($value);
    }
}