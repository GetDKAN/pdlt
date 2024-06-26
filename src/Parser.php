<?php

namespace PDLT;

/**
 * Date format parser.
 *
 * Use the supplied grammar to convert the supplied date format to an
 * intermediary AST language with the same directive tokens as Strptime.
 */
class Parser implements ParserInterface {

  /**
   * @var \PDLT\GrammarInterface
   */
  public $grammar;

  /**
   * Creates a date format parser.
   *
   * @param \PDLT\GrammarInterface $grammar
   *   Grammar for date format being parsed.
   */
  public function __construct(GrammarInterface $grammar) {
    $this->grammar = $grammar;
  }

  /**
   * Break up supplied date format string into lexemes.
   *
   * This function builds an abstract syntax tree from the supplied string. The
   * AST is hardly a tree though since there is only one parent node with a
   * bunch of children, it's more of a syntax list. As such, the return value
   * of this function is a list of all child nodes in the tree.
   *
   * @param string $input_format
   *   Date format string to lex.
   *
   * @return \PDLT\TokenInterface[]
   *   AST token list.
   */
  protected function lex(string $input_format): array {
    // Initialize our grammar reference, AST, and local DB output format string.
    $grammar_ptr = &$this->grammar;
    $syntax = [];
    $literal = '';

    // Iterate through the input format string character-by-character in order
    // to incrementally build an AST from the input.
    foreach (str_split($input_format) as $char) {
      $literal .= $char;
      // If the current character doesn't exist in this level of the AST, we
      // have no path to tokenizing this character...
      if (!isset($grammar_ptr[$char])) {
        // If no valid AST character was found mid-token, the given format is
        // invalid, and an exception should be thrown.
        if (strlen($literal) > 1) {
          throw new UnknownTokenException(sprintf('Invalid format provided; token "%s" not found in grammar "%s".', $literal, get_class($this->grammar)));
        }
        // Since we have no path to tokenize this character, we want to go back
        // to the top level of the AST...
        $grammar_ptr = &$this->grammar;
        // Write the current character to the output format...
        $syntax[] = new LiteralToken($literal);
        // And clear out the literal buffer now that the character has been
        // written to the output.
        $literal = '';
      }
      // Otherwise, we have a path forward...
      else {
        // If the current character is not an end node in the AST...
        if (is_array($grammar_ptr[$char])) {
          // Continue down the AST path.
          $grammar_ptr = &$grammar_ptr[$char];
        }
        // Otherwise, if the current character is an end node...
        else {
          // Write the content of the end node to the output format...
          $syntax[] = new DirectiveToken($literal);
          // Go back to the top level of the AST...
          $grammar_ptr = &$this->grammar;
          // And clear out the literal buffer now that the character(s) has been
          // written to the output...
          $literal = '';
        }
      }
    }

    return $syntax;
  }

  /**
   * Generate an Abstract Syntax Tree (AST) from the given date format.
   *
   * @param string $input
   *   Input date format.
   *
   * @return array
   *   Generated AST.
   */
  public function parse(string $input): array {
    // Break input format up into list of lexemes.
    $syntax = $this->lex($input);

    // Since the language we're working with is linear and does not contain
    // complex expressions, a multi-level AST doesn't need to be generated, the
    // tokens generated by the lexer can be returned as a single level "AST".
    return $syntax;
  }

}
