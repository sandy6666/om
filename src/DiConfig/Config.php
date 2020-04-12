<?php
/**
 *
 * @package     om
 * @author      Jayanka Ghosh
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        https://github.com/jayankaghosh/
 */

namespace Om\DiConfig;


use Om\DiConfig\Config\Preference;
use Om\DiConfig\Config\Type;

class Config
{
    /**
     * @var array
     */
    private $config;

    /**
     * Config constructor.
     * @param array|null $config
     */
    public function __construct(?array $config = [])
    {
        $this->config = [
            'preferences' => [],
            'types' => []
        ];
        if ($config) {
            if (isset($config['preferences']) && is_array($config['preferences'])) {
                $this->setPreferences($config['preferences']);
            }
            if (isset($config['types']) && is_array($config['types'])) {
                $this->setTypes($config['types']);
            }
        }
    }

    /**
     * @param array $config
     * @return Config
     */
    public static function fromArray(array $config)
    {
        return new Config($config);
    }

    /**
     * @param Preference[] $preferences
     * @return $this
     */
    public function setPreferences(array $preferences)
    {
        $this->config['preferences'] = [];
        foreach ($preferences as $preference) {
            $this->addPreference($preference);
        }
        return $this;
    }

    /**
     * @param array|Preference $preference
     * @return Config
     */
    public function addPreference($preference)
    {
        if (!$preference instanceof Preference) {
            $preference = new Preference($preference['for'], $preference['type']);
        }
        $this->config['preferences'][$preference->getFor()] = $preference;
        return $this;
    }

    /**
     * @return Preference[]
     */
    public function getPreferences()
    {
        return $this->config['preferences'];
    }

    /**
     * @param Type[] $types
     * @return $this
     */
    public function setTypes(array $types)
    {
        $this->config['types'] = [];
        foreach ($types as $type) {
            $this->addType($type);
        }
        return $this;
    }

    /**
     * @param array|Type $type
     * @return $this
     */
    public function addType($type)
    {
        if (!$type instanceof Type) {
            $type = new Type(
                $type['name'],
                $type['arguments'] ?? [],
                $type['plugins'] ?? []
            );
        }
        $this->config['types'][$type->getName()] = $type;
        return $this;
    }

    /**
     * @return Type[]
     */
    public function getTypes()
    {
        return $this->config['types'];
    }
}