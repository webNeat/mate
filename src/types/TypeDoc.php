<?php
namespace Wn\Mate;

/**
 * @mate
 * @type TypeDoc
 * @field string $name
 * @field string $description
 * @field array<Parameter> $fields
 */
class TypeDoc {
  public $name;
  public $description;
  public $fields;

  public static function of(
    string $name,
    string $description,
     array $fields
  ): TypeDoc {
    $data = new TypeDoc;
    $data->name = $name;
    $data->description = $description;
    $data->fields = $fields;
    return $data;
  }
}

