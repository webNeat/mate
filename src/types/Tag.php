<?php
namespace Wn\Mate;

/**
 * @mate
 * @type Tag
 * @field string $name
 * @field string $content
 */
class Tag {
  public $name;
  public $content;

  public static function of(string $name, string $content): Tag {
    $data = new Tag;
    $data->name = $name;
    $data->content = $content;
    return $data;
  }
}
