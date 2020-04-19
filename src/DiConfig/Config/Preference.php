<?php
/**
 *
 * @package     om
 * @author      Jayanka Ghosh
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        https://github.com/jayankaghosh/
 */

namespace Om\DiConfig\Config;


class Preference
{
    /**
     * @var string
     */
    private $for;
    /**
     * @var string
     */
    private $type;

    /**
     * Preference constructor.
     * @param string $for
     * @param string $type
     */
    public function __construct(string $for, string $type)
    {
        $this->for = $for;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getFor(): string
    {
        return $this->for;
    }

    /**
     * @param string $for
     */
    public function setFor(string $for): void
    {
        $this->for = $for;
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
     * @return array
     */
    public function toArray()
    {
        return [
            'for' => $this->getFor(),
            'type' => $this->getType()
        ];
    }
}