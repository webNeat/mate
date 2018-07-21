<?php
namespace Wn\Mate\Classes;

class TestCase extends \PHPUnit\Framework\TestCase {
  public function assertThrows(callable $fn, string $msg) {
    try {
      $fn();
      $this->assertFalse(true, "It didn't throw!");
    } catch (\Exception $e) {
      $this->assertEquals($msg, $e->getMessage());
    }
  }
}
