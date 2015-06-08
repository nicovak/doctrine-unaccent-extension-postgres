<?php
namespace Acme\DemoBundle\Admin\Doctrine\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

/**
 * Unaccent string using postgresql extension unaccent :
 * http://www.postgresql.org/docs/current/static/unaccent.html
 *
 * Usage : StringFunction UNACCENT(string)
 *
 */
class UnaccentString extends FunctionNode
{
    private $string;

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return 'UNACCENT(' . $this->string->dispatch($sqlWalker) .")";
    }

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->string = $parser->StringPrimary();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

}