<?php
/**
 *
 * @package     om
 * @author      Jayanka Ghosh
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        https://github.com/jayankaghosh/
 */

namespace Om\DiConfig\Config\Type;


use Om\DiConfig\Config\Type\Argument\Item;

class Argument
{

    const TYPE_ARRAY = 'array';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_COMPUTED = 'computed';
    const TYPE_CONST = 'const';
    const TYPE_NULL = 'null';
    const TYPE_NUMBER = 'number';
    const TYPE_OBJECT = 'object';
    const TYPE_STRING = 'string';


    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $type;

    /**
     * @var mixed
     */
    private $value;

    /**
     * Argument constructor.
     * @param string $name
     * @param string $type
     * @param $value
     */
    public function __construct(string $name, string $type = self::TYPE_NULL, $value = null)
    {
        $this->setName($name);
        $this->setType($type);
        $this->setValue($value);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): void
    {
        if (is_array($value)) {
            $items = [];
            foreach ($value as $key => $item) {
                if (!$item instanceof Item) {
                    $item = new Item($item['name'], $item['type'], $item['value'] ?? null);
                }
                $items[$item->getName()] = $item;
            }
            $value = $items;
        }
        $this->value = $value;
    }
}