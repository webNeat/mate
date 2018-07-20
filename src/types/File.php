<?php
namespace Wn\Mate;

/**
 * @mate
 * @type File
 * @field string $path
 * @field string $content
 */
class File {
  public $path;
  public $content;

  public static function of(
    string $path,
    string $content
  ): File {
    $data = new File;
    $data->path = realpath($path);
    $data->content = $content;
    return $data;
  }
}
