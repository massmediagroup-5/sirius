<?php

namespace AppBundle\Query;

use Doctrine\ORM\Query\AST\Functions\FunctionNode,
    Doctrine\ORM\Query\Lexer;

/**
 * @author Zimm
 */
class SubQuery extends FunctionNode
{
    public $expr1 = null;

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->expr1 = $parser->Subselect();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return '(' . $this->expr1->dispatch($sqlWalker) . ')';
    }
}
