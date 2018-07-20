<?php
namespace Wn\Mate;

/**
 * @mate
 * @type UnknownBlock
 * @field string $type //= 'unknown'
 * @field string $name
 * @field string $description
 * @field array<Parameter> $fields
 * @field array<Tag> $tags
 */
class UnknownBlock extends Block {
  public static function of(
    string $description,
     array $tags
  ): UnknownBlock {
    $data = new UnknownBlock;
    $data->description = $description;
    $data->tags = $tags;
    return $data;
  }
}

