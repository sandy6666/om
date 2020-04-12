<?php
/**
 *
 * @package     om
 * @author      Jayanka Ghosh
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        https://github.com/jayankaghosh/
 */

namespace Om\Code\Generators;


class MethodGenerator
{
    /**
     * @var int
     */
    private $padding;

    /**
     * @var string
     */
    private $scope = "";

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $arguments = [];

    /**
     * @var string
     */
    private $body = "\n\n";

    /**
     * @var string[]
     */
    private $comments = [];

    /**
     * @var string[]
     */
    private $returnType = null;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return $this
     */
    public function setBody(string $body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @param string $type
     * @param string $name
     * @param null $default
     * @param bool $nullable
     * @return $this
     */
    public function addArgument($type, $name, $default = null, $nullable = false)
    {
        $argument = [
            'type'      =>  $type,
            'name'      =>  $name,
            'nullable'  =>  $nullable
        ];
        if (!$default instanceof NullExpr) {
            $argument['default'] = $default;
        }
        $this->arguments[] = $argument;
        return $this;
    }

    /**
     * @return string
     */
    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * @param string $scope
     * @return $this
     */
    public function setScope(string $scope)
    {
        $this->scope = $scope;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getComments(): array
    {
        return $this->comments;
    }

    /**
     * @param string $comment
     * @return $this
     */
    public function addComment($comment)
    {
        $this->comments[] = $comment;
        return $this;
    }

    /**
     * @param string $variable
     * @param bool $stringify
     * @return string
     */
    protected function getVariableRenderedValue($variable, $stringify = true)
    {
        if ($variable instanceof Expr) {
            return $variable;
        }
        if (is_bool($variable)) {
            $variable = $variable ? 'true' : 'false';
        } else if (is_null($variable)) {
            $variable = 'null';
        } else if (is_array($variable)) {
            if (!count($variable)) {
                $variable = '[]';
            } else {
                $newVariableValues = [];
                $isSequential = array_keys($variable)[0] === 0;
                foreach ($variable as $key => $value) {
                    $newVariableValue = '';
                    if (!$isSequential) {
                        $newVariableValue .= sprintf('"%s" => ', $key);
                    }
                    $value = $this->getVariableRenderedValue($value);
                    $newVariableValue .= $value;
                    $newVariableValues[] = $newVariableValue;
                }
                $variable = '[' . implode(', ', $newVariableValues) . ']';
            }

        } else if (is_string($variable)) {
            if ($stringify && (!strlen($variable) || strlen($variable) && $variable[0] !== '\\')) {
                $variable = '"' . $variable . '"';
            }
        }
        return $variable;
    }


    /**
     * @return string
     */
    public function generate() {
        $padding = $this->getPadding();
        $content = "";
        $arguments = [];
        foreach ($this->getArguments() as $argument) {
            if ($argument['type'] && $argument['nullable']) {
                $argument['type'] = '?' . $argument['type'];
            }
            $argumentDefinition  = $argument['type']." ".$argument['name'];
            if (array_key_exists('default', $argument)) {
                $argument['default'] = $this->getVariableRenderedValue($argument['default']);
                $argumentDefinition .= " = ".$argument['default'];
            }
            $arguments[] = trim($argumentDefinition);
        }
        if (count($this->getComments())) {
            $content .= "$padding/**";
            foreach ($this->getComments() as $comment) {
                $content .= "\n$padding * $comment";
            }
            $content .= "\n$padding */\n";
        }
        $content .= $padding.$this->getScope()." function ".$this->getName()."(".implode(", ", $arguments).")";
        $returnType = $this->getReturnType();
        if ($returnType) {
            $returnTypeNullable = $returnType['nullable'];
            $returnType = $returnType['type'];
            $returnType = $this->getVariableRenderedValue($returnType, false);
            if ($returnTypeNullable) {
                $returnType = '?' . $returnType;
            }
            $content .= ': ' . $returnType;
        }
        $content .= " {";
        $originalPadding = $this->padding;
        $this->setPadding($originalPadding+1);
        $content .= preg_replace("/[\r\n]/", "\r\n".$this->getPadding(), "\n".$this->getBody());
        $this->setPadding($originalPadding);
        $content .= "\n$padding}";
        return $content;
    }

    public function __toString()
    {
        return $this->generate();
    }

    /**
     * @return int
     */
    public function getPadding()
    {
        return str_repeat("\t", (int)$this->padding);
    }

    /**
     * @param int $padding
     * @return $this
     */
    public function setPadding($padding)
    {
        $this->padding = (int)$padding;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReturnType()
    {
        return $this->returnType;
    }

    /**
     * @param mixed $returnType
     * @return MethodGenerator
     */
    public function setReturnType($returnType)
    {
        $this->returnType = $returnType;
        return $this;
    }
}