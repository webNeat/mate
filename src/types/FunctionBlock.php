<?php
namespace Wn\Mate;

/**
 * @mate
 * @type FunctionBlock
 * @field string $type //= 'function'
 * @field string $name
 * @field string $description
 * @field array<Parameter> $args
 * @field string $returnType
 * @field array<Tag> $tags
 */
class FunctionBlock extends Block {
  public $type = 'function';
  public $name;
  public $args;
  public $returnType;

  public static function of(
    string $name,
    string $description,
     array $args,
    string $returnType,
     array $tags
  ): FunctionBlock {
    $data = new FunctionBlock;
    $data->name = $name;
    $data->description = $description;
    $data->args = $args;
    $data->returnType = $returnType;
    $data->tags = $tags;
    return $data;
  }
}

