<?php
/**
 *
 * @package     om
 * @author      Codilar Technologies
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        http://www.codilar.com/
 */

namespace Om\Tools;


/**
 * Class JsonSerializer
 */
class JsonSerializer extends \SimpleXMLElement implements \JsonSerializable
{
    const ATTRIBUTE_INDEX = "@attributes";
    const CONTENT_NAME = "_content";

    /**
     * SimpleXMLElement JSON serialization
     *
     * @return array
     *
     * @link http://php.net/JsonSerializable.jsonSerialize
     * @see \JsonSerializable::jsonSerialize
     * @see https://stackoverflow.com/a/31276221/36175
     */
    public function jsonSerialize()
    {
        $array = [];

        if ($this->count()) {
            // serialize children if there are children
            /**
             * @var string $tag
             * @var JsonSerializer $child
             */
            foreach ($this as $tag => $child) {
                $temp = $child->jsonSerialize();
                $attributes = [];

                foreach ($child->attributes() as $name => $value) {
                    $attributes["$name"] = (string)$value;
                }

                $array[$tag][] = array_merge($temp, [self::ATTRIBUTE_INDEX => $attributes]);
            }
        } else {
            // serialize attributes and text for a leaf-elements
            $temp = (string)$this;

            // if only contains empty string, it is actually an empty element
            if (trim($temp) !== "") {
                $array[self::CONTENT_NAME] = $temp;
            }
        }

        if ($this->xpath('/*') == array($this)) {
            // the root element needs to be named
            $array = [$this->getName() => $array];
        }

        return $array;
    }
}