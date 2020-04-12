<?php
/**
 *
 * @package     om
 * @author      Jayanka Ghosh
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        https://github.com/jayankaghosh/
 */

namespace Om\Code\Generators;


class NullExpr extends Expr
{
    public function __construct()
    {
        parent::__construct(null);
    }
}