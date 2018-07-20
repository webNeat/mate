<?php
namespace Wn\Mate;

/**
 * @mate
 * @type TypeBlock
 * @field string $type //= 'type'
 * @field string $name
 * @field string $description
 * @field array<Parameter> $fields
 * @field array<Tag> $tags
 */
class TypeBlock extends Block {
  public $type = 'type';
  public $name;
  public $fields;

  public static function of(
    string $name,
    string $description,
     array $fields,
     array $tags
  ): TypeBlock {
    $data = new TypeBlock;
    $data->name = $name;
    $data->description = $description;
    $data->fields = $fields;
    $data->tags = $tags;
    return $data;
  }
}

