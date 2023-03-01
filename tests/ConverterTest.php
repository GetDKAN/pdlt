<?php

namespace PDLT\tests;

use PDLT\Converter;
use PDLT\Parser;
use PDLT\Compiler;
use PDLT\Grammar\Strptime as StrptimeGrammar;
use PDLT\CompilationMap\MySQL as MySQLCompilationMap;

use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Converter class.
 */
class ConverterTest extends TestCase {

  /**
   * Test convert() in parent class.
   */
  public function testStrptimeToMySqlConverter() {
    $grammar = new StrptimeGrammar();
    $strptime_parser = new Parser($grammar);
    $mysql_compilation_map = new MySQLCompilationMap();
    $mysql_compiler = new Compiler($mysql_compilation_map);
    $converter = new Converter($strptime_parser, $mysql_compiler);

    $result = $converter->convert('%Y-%m-%d');
    $this->assertEquals('%Y-%c-%d', $result);
  }

}
