<?php
/**
 *
 * @package     om
 * @author      Jayanka Ghosh
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        https://github.com/jayankaghosh/
 */

namespace Om\Registry;


interface RegistryKeysInterface
{
    const OBJECT_BUCKET_KEY_PREFIX = '___object_bucket_';
    const OBJECT_MANAGER_KEY = '___object_manager';
    const DI_CONFIG_KEY = '___di_config';
    const GENERATED_DIRECTORY_PATH_KEY = '___generated_directory_path';
    const CAN_GENERATE_CLASS_KEY = '___can_generate_class';
}