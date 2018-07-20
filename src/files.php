<?php
namespace Wn\Mate;

use Tarsana\Functional as F;

/**
 * Creates a `File` instance from a path.
 *
 * @function load_file
 * @param  string $path
 * @return File
 */
function load_file(string $path): File {
  return File::of($path, file_get_contents($path));
}

/**
 * Refreshes the content and hash of a file and return `true`
 * if the file has been changed and `false` otherwise.
 *
 * @function refresh_file
 * @param  File   $file
 * @return File
 */
function refresh_file(File $file): File {
  $content = file_get_contents($file->path);
  if ($content == $file->content)
    return false;
  $file->content = $content;
  return true;
}

/**
 * Gets paths to all PHP source files inside a directory recursively.
 *
 * @function source_paths
 * @param  string $dirPath
 * @return array<string>
 */
function source_paths(string $dirPath): array {
  $dirPath = rtrim($dirPath, "/\\") . '/';
  $paths = glob($dirPath . '*', GLOB_MARK);
  $files = F\filter(F\endsWith('.php'), $paths);
  $dirs = F\filter(F\endsWith('/'), $paths);
  return F\concat($files, F\chain(alias('source_paths'), $dirs));
}

/**
 * Reads and parses the content of a JSON
 * file. Returns `[]` if the file is missing.
 *
 * @function load_json
 * @param  string $path
 * @return array
 */
function load_json(string $path): array {
  if (is_file($path))
    return json_decode(file_get_contents($path), true);
  return [];
}

/**
 * Saves an `array` into a file as JSON.
 *
 * @param  string $path
 * @param  array  $data
 * @return void
 */
function save_json(string $path, array $data) {
  file_put_contents($path, json_encode($data));
}

function write_file(string $content, string $path) {
  $dirPath = dirname($path);
  if (!file_exists($dirPath))
    mkdir($dirPath, 0777, true);
  file_put_contents($path, $content);
}
