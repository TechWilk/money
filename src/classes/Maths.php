<?php

class Maths
{
  static public function calculateString($string) {
    $string = trim($string);
    $string = preg_replace("/(\+|-|\*|\/|\(|\))/i", " \${1} ", $string); // ensure there are spaces between numbers and symbols
    $string = str_replace('  ', ' ', $string);

    $parser = new \Math\Parser();
    return $parser->evaluate($string);
  }
}