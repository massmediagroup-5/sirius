<?php

namespace AppBundle\Query;

use Doctrine\ORM\Query\AST\Functions\FunctionNode,
    Doctrine\ORM\Query\Lexer;

/**
 * @author Zimm
 */
class Coalesce extends FunctionNode
{
    private $expr1;

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->expr1[] = $parser->ArithmeticExpression();
        while ($parser->getLexer()->isNextToken(Lexer::T_COMMA)) {
            $parser->match(Lexer::T_COMMA);

            $this->expr1[] = $parser->ArithmeticExpression();
        }
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        $coalesce = array_map(function ($expr) use ($sqlWalker) {
            return $sqlWalker->walkArithmeticPrimary($expr);
        }, $this->expr1);

        $coalesce = implode(', ', $coalesce);

        return 'COALESCE(' . $coalesce . ')';
    }
}
