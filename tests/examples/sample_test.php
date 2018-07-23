<?php
namespace My\Namespace;

use Tarsana\Functional as F;

class SampleTest extends \Wn\Mate\Classes\TestCase
{
  function test_add() {
    $this->assertEquals(add(5, 2), 7);
    $this->assertEquals(add(0, 1), 1);
    $this->assertThrows(function() {
    	add('Hey', 'you');
    },
    "'Hey' is not a number!");
  }
}

