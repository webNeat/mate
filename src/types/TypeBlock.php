<?php
namespace Wn\Mate;

/**
 * @mate
 * @type TypeBlock
 * @field string $type //= 'type'
 * @field string $name
 * @field string $description
 * @field array<Parameter> $fields
 * @field object<array<string>> $tags
 */
class TypeBlock extends Block {
  public $type = 'type';
  public $name;
  public $fields;

  public static function of(
    string $name,
    string $description,
     array $fields,
     $tags = null
  ): TypeBlock {
    $data = new TypeBlock;
    $data->name = $name;
    $data->description = $description;
    $data->fields = $fields;
    $data->tags = $tags ?: new \stdClass;
    return $data;
  }
}

