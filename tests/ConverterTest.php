<?php

namespace PDLT\Tests;

use PDLT\Converter;
use PDLT\Parser;
use PDLT\Compiler;
use PDLT\Grammar\Strptime;
use PDLT\CompilationMap\MySQL;

use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Converter class.
 */
class ConverterTest extends TestCase {

  public function provideFormats() {
    return [
      'from-readme' => ['%c/%e/%y', '%-m/%-d/%y'],
      ['%a', '%a'],

      ['%W', '%A'],

      ['%w', '%w'],
      ['%d', '%d'],
      ['%b', '%b'],

      ['%M', '%B'],

      ['%m', '%m'],
      ['%y', '%y'],
      ['%Y', '%Y'],
      ['%H', '%H'],
      ['%I', '%I'],
      ['%p', '%p'],

      ['%i', '%M'],
      ['%s', '%S'],

      ['%f', '%f'],
      ['%U', '%U'],

      ['%u', '%W'],

      ['%%', '%%'],
    ];
  }

  /**
   * @dataProvider provideFormats
   */
  public function testStrptimeToMySqlConverter($expected, $input_format) {
    $converter = new Converter(
      new Parser(new Strptime()),
      new Compiler(new MySQL())
    );

    $this->assertEquals($expected, $converter->convert($input_format));
  }

}
