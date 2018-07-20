<?php
namespace Wn\Mate;

/**
 * @mate
 * @type Parameter
 * @field string $name
 * @field string $type
 * @field string $description
 */
class Parameter {
  public $name;
  public $type;
  public $description;

  public static function of(
    string $name,
    string $type,
    string $description = ''
  ): Parameter {
    $data = new Parameter;
    $data->name = $name;
    $data->type = $type;
    $data->description = $description;
    return $data;
  }
}

