<?php
/**
 *
 * @package     om
 * @author      Jayanka Ghosh
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        https://github.com/jayankaghosh/
 */

namespace Om\ObjectManager;


use Om\Registry\Registry;

class AutoLoader
{

    const GENERATED_FILE_PATH = "var/generated/";

    /**
     * @var bool
     */
    private $instantiated;

    public function __construct()
    {
        $this->instantiated = false;
    }

    public function register()
    {
        if (!$this->instantiated) {
            spl_autoload_register([$this, 'handle']);
            $this->instantiated = true;
        }
    }

    /**
     * @param string $className
     */
    private function handle($className)
    {
        $generatedDirectoryPath = Registry::get(Registry::GENERATED_DIRECTORY_PATH_KEY);
        $className = $generatedDirectoryPath . DIRECTORY_SEPARATOR . str_replace('\\', '/', $className) . '.php';
        @include_once $className;
    }
}
