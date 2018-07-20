<?php
namespace Wn\Mate;

/**
 * @mate
 * @type Doc
 * @field string $path;
 * @field string $title;
 * @field string $header;
 * @field array<TypeDoc> $types;
 * @field array<FunctionDoc> $functions;
 */
class Doc {
  public $path;
  public $title;
  public $header;
  public $types;
  public $functions;

  public static function of(
    string $path,
    string $title,
    string $header,
     array $types,
     array $functions
  ): Doc {
    $data = new Doc;
    $data->path = $path;
    $data->title = $title;
    $data->header = $header;
    $data->types = $types;
    $data->functions = $functions;
    return $data;
  }
}
