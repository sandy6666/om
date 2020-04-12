<?php
/**
 *
 * @package     om
 * @author      Jayanka Ghosh
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        https://github.com/jayankaghosh/
 */
declare(strict_types=1);

namespace Om;


use Om\DependencyInjection\Resolvers\TypeResolver;
use Om\DiConfig\Config;
use Om\ObjectManager\AutoLoader;
use Om\ObjectManager\ObjectManager;
use Om\Registry\Registry;

class OmFactory
{
    /**
     * @var Config|null
     */
    private $diConfig;
    /**
     * @var null|string
     */
    private $generatedDirectoryPath;

    /**
     * OmFactory constructor.
     * @param Config|null $diConfig
     * @param bool $generateClasses
     * @param null|string $generatedDirectoryPath
     */
    public function __construct(
        ?Config $diConfig = null,
        ?string $generatedDirectoryPath = null
    )
    {
        $this->diConfig = $diConfig;
        $this->generatedDirectoryPath = $generatedDirectoryPath;
    }

    /**
     * @return null
     */
    public function getDiConfig()
    {
        return $this->diConfig;
    }

    /**
     * @param null $diConfig
     */
    public function setDiConfig($diConfig): void
    {
        $this->diConfig = $diConfig;
    }

    /**
     * @return null|string
     */
    public function getGeneratedDirectoryPath(): ?string
    {
        if ($this->generatedDirectoryPath) {
            $path = rtrim($this->generatedDirectoryPath, '/');
        } else {
            $path = null;
        }
        return $path;
    }

    /**
     * @param null|string $generatedDirectoryPath
     */
    public function setGeneratedDirectoryPath(?string $generatedDirectoryPath): void
    {
        $this->generatedDirectoryPath = $generatedDirectoryPath;
    }

    /**
     * @return ObjectManager
     */
    public function getInstance()
    {
        if (!$this->diConfig) $this->diConfig = new Config();
        $typeResolver = new TypeResolver($this->diConfig);
        $objectManager = new ObjectManager($typeResolver);
        $generatedDirectoryPath = $this->getGeneratedDirectoryPath();
        Registry::set(Registry::OBJECT_MANAGER_KEY, $objectManager);
        Registry::set(Registry::DI_CONFIG_KEY, $this->diConfig);
        Registry::set(Registry::GENERATED_DIRECTORY_PATH_KEY, $generatedDirectoryPath);
        Registry::set(Registry::CAN_GENERATE_CLASS_KEY, !!$generatedDirectoryPath);
        if ($generatedDirectoryPath) {
            /** @var AutoLoader $autoloader */
            $autoloader = new AutoLoader();
            $autoloader->register();
        }
        return $objectManager;
    }

}