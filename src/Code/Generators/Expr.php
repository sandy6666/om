<?php
/**
 *
 * @package     om
 * @author      Jayanka Ghosh
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        https://github.com/jayankaghosh/
 */

namespace Om\Code\Generators;


class Expr
{
    private $expr;

    /**
     * Expr constructor.
     * @param $expr
     */
    public function __construct($expr)
    {
        $this->expr = $expr;
    }

    /**
     * @return mixed
     */
    public function getExpr()
    {
        return $this->expr;
    }

    public function __toString()
    {
        return $this->getExpr();
    }
}