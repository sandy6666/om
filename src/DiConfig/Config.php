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
use Om\Tools\JsonSerializer;

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
     * @param Config|null $mergeWith
     * @return Config
     */
    public static function fromArray(array $config, ?Config $mergeWith = null)
    {
        if ($mergeWith instanceof Config) {
            $config = array_merge_recursive($mergeWith->toArray(), $config);
        }
        return new Config($config);
    }

    /**
     * @param string $xml
     * @param null|Config $mergeWith
     * @return Config
     */
    public static function fromXml(string $xml, ?Config $mergeWith = null)
    {
        $xmlObject = @new JsonSerializer($xml);
        $config = \json_decode(\json_encode($xmlObject), true)['config'] ?? [];
        $preferences = $config['preference'] ?? [];
        $types = $config['type'] ?? [];
        $result = [
            'preferences' => [],
            'types' => []
        ];
        if ($mergeWith instanceof Config) {
            $result = $mergeWith->toArray();
        }
        foreach ($preferences as $preference) {
            self::parsePreference($result['preferences'], $preference);
        }
        foreach ($types as $type) {
            self::parseType($result['types'], $type);
        }
        return self::fromArray($result);
    }

    protected static function parsePreference(&$result, $preference)
    {
        $result[$preference[JsonSerializer::ATTRIBUTE_INDEX]['for']] = [
            'for' => $preference[JsonSerializer::ATTRIBUTE_INDEX]['for'],
            'type' => $preference[JsonSerializer::ATTRIBUTE_INDEX]['type']
        ];
    }

    protected static function parseType(&$result, $type)
    {
        $typeName = $type[JsonSerializer::ATTRIBUTE_INDEX]['name'];
        if (!isset($result[$typeName])) {
            $result[$typeName] = [
                'name' => $typeName,
                'arguments' => [],
                'plugins' => []
            ];
        }
        $plugins = $type['plugin'] ?? [];
        $argumentDefinition = $type['arguments'] ?? [];
        $arguments = [];
        foreach ($argumentDefinition as $argumentList) {
            $argumentList = $argumentList['argument'] ?? [];
            foreach ($argumentList as $argument) {
                $arguments[$argument[JsonSerializer::ATTRIBUTE_INDEX]['name']] = $argument;
            }
        }

        foreach ($plugins as $plugin) {
            self::parseTypePlugin($result[$typeName]['plugins'], $plugin);
        }

        foreach ($arguments as $argument) {
            self::parseTypeArgument($result[$typeName]['arguments'], $argument);
        }
    }

    protected static function parseTypePlugin(&$result, $plugin)
    {
        $name = $plugin[JsonSerializer::ATTRIBUTE_INDEX]['name'];
        $type = $plugin[JsonSerializer::ATTRIBUTE_INDEX]['type'];
        $result[$name] = [
            'name' => $name,
            'type' => $type
        ];
    }

    protected static function parseTypeArgument(&$result, $argument)
    {
        $type = $argument[JsonSerializer::ATTRIBUTE_INDEX]['xsi:type'];
        $value = null;
        if ($type === 'array') {
            $value = [];
            $items = $argument['item'] ?? [];
            foreach ($items as $item) {
                self::parseTypeArgument($value, $item);
            }
        } else {
            $value = $argument[JsonSerializer::CONTENT_NAME];
        }
        $result[] = [
            'name' => $argument[JsonSerializer::ATTRIBUTE_INDEX]['name'],
            'type' => $type,
            'value' => $value
        ];
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

    /**
     * @return array
     */
    public function toArray()
    {
        $result = [
            'preferences' => [],
            'types' => []
        ];
        foreach ($this->getPreferences() as $preference) {
            $result['preferences'][$preference->getFor()] = $preference->toArray();
        }
        foreach ($this->getTypes() as $type) {
            $result['types'][$type->getName()] = $type->toArray();
        }
        return $result;
    }
}