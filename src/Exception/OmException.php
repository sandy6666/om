<?php
/**
 *
 * @package     om
 * @author      Jayanka Ghosh
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        https://github.com/jayankaghosh/
 */

namespace Om\Exception;


use Throwable;

class OmException extends \Exception
{
    public function __construct(string $message = "", $messageArguments = [], int $code = 0, Throwable $previous = null)
    {
        $message = vsprintf($message, $messageArguments);
        parent::__construct($message, $code, $previous);
    }
}