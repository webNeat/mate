<?php
namespace Wn\Mate;

use Tarsana\Functional as F;

class ParseTest extends \Wn\Mate\Classes\TestCase
{
  function test_make_parameter() {
    $this->assertEquals(make_parameter(' string   $name  ...'), Parameter::of('$name', 'string', '...'));
    $this->assertEquals(make_parameter('array< T > $fns '), Parameter::of('$fns', 'array< T >'));
    $this->assertEquals(make_parameter('((int, int): int) $fn '), Parameter::of('$fn', '((int, int): int)'));
  }

  function test_tag_from_line() {
    $this->assertEquals(tag_from_line('@tag value'), ['tag', 'value']);
    $this->assertEquals(tag_from_line('@tag    value is here'), ['tag', 'value is here']);
    $this->assertEquals(tag_from_line('@tag-with-dashes value  spaces'), ['tag-with-dashes', 'value  spaces']);
  }
}

