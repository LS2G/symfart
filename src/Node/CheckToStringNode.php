<?php

namespace Twig\Node;

use Twig\Node;
use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;

class CheckToStringNode extends Node
{
    public function __construct(AbstractExpression $expr)
    {
        parent::__construct(['expr' => $expr], [], $expr->getTemplateLine(), $expr->getNodeTag());
    }
    public function compile(Compiler $compiler)
    {
        $compiler
            ->raw('$this->sandbox->ensureToStringAllowed(')
            ->subcompile($this->getNode('expr'))
            ->raw(')')
        ;
    }
}
?>