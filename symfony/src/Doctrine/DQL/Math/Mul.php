<?php
/**
 * @author Sergey Hashimov <hashimov.sergey@gmail.com>
 */

namespace App\Doctrine\DQL\Math;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Query\AST\ArithmeticExpression;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\SqlWalker;

class Mul extends FunctionNode
{
    /**
     * @var ArithmeticExpression
     */
    private $expr1;
    /**
     * @var ArithmeticExpression
     */
    private $expr2;

    public function getSql(SqlWalker $sqlWalker)
    {
        return "({$sqlWalker->walkArithmeticPrimary($this->expr1)} * {$sqlWalker->walkArithmeticPrimary($this->expr2)})";
    }

    /**
     * @param Parser $parser
     * @throws QueryException
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->expr1 = $parser->ArithmeticExpression();
        $parser->match(Lexer::T_COMMA);
        $this->expr2 = $parser->ArithmeticExpression();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}