<?php

namespace Tests\Unit;

use \Maths;

class MathsTest extends BaseTestCase
{
  public function providerTestValidCalculations()
  {
    return [
      [ '1 + 1', 2 ],
      [ '5 / 5', 1 ],
      [ '5.89 + 4.98', 10.87 ],
      [ '36.34-5.28', 31.06 ],
      [ '5+2-3/4', 6.25 ],
    ];
  }

  /**
  * @param string $calculation Calculation to be performed
  * @param mixed $expected Expected result of the calculation
  *
  * @dataProvider providerTestValidCalculations
  */
  public function testValidCalculations($calculation, $expected)
  {
    $result = Maths::calculateString($calculation);

    $this->assertEquals($expected, $result);
  }

  public function providerTestInvalidCalculations()
  {
    return [
      [ 'not-an-calculation' ],
      [ 'another-not-an-email' ],
      [ '' ],
    ];
  }

  /**
  * @param string $calculation
  * @expectedException        InvalidArgumentException
  *
  * @dataProvider providerTestInvalidCalculations
  */
  public function testInvalidCalculations($calculation)
  {
    $result = Maths::calculateString($calculation);

    //$this->assertNotEqual((string)$emailObject, $email);
  }
}