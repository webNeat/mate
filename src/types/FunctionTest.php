<?php
namespace Wn\Mate;

/**
 * @mate
 * @type FunctionTest
 * @field string $name
 * @field string $code
 */
class FunctionTest {
  public $name;
  public $code;

  public static function of(
    string $name,
    string $code
  ): FunctionTest {
    $data = new FunctionTest;
    $data->name = $name;
    $data->code = $code;
    return $data;
  }
}

