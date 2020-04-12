<?php
declare(strict_types=1);

namespace Om\Registry;


class Registry implements RegistryKeysInterface
{
    private static $data = [];

    /**
     * @param string $name
     * @return mixed|null
     */
    public static function get($name)
    {
        return self::$data[$name] ??  null;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public static function set($name, $value)
    {
        self::$data[$name] = $value;
    }

    /**
     * @param $name
     */
    public static function delete($name)
    {
        unset(self::$data[$name]);
    }
}