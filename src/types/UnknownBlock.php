<?php
namespace Wn\Mate;

/**
 * @mate
 * @type UnknownBlock
 * @field string $type //= 'unknown'
 * @field string $name
 * @field string $description
 * @field array<Parameter> $fields
 * @field object<array<string>> $tags
 */
class UnknownBlock extends Block {
  public static function of(
    string $description,
     $tags = null
  ): UnknownBlock {
    $data = new UnknownBlock;
    $data->description = $description;
    $data->tags = $tags ?: new \stdClass;
    return $data;
  }
}

