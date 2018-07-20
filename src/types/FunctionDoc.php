<?php
namespace Wn\Mate;

/**
 * @mate
 * @type FunctionDoc
 * @field string $name
 * @field string $description
 * @field string $signature
 * @field string $example
 */
class FunctionDoc {
  public $name;
  public $description;
  public $signature;
  public $example;

  public static function of(
    string $name,
    string $description,
    string $signature,
    string $example
  ): FunctionDoc {
    $data = new FunctionDoc;
    $data->name = $name;
    $data->description = $description;
    $data->signature = $signature;
    $data->example = $example;
    return $data;
  }
}
