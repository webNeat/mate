<?php
namespace Wn\Mate;

/**
 * @mate
 * @type Test
 * @field string $path;
 * @field string $namespace;
 * @field array<string> $uses;
 * @field string $name;
 * @field string $parent;
 * @field array<FunctionTest> $functions;
 * @field string $globals;
 */
class Test {
  public $path;
  public $namespace;
  public $uses;
  public $name;
  public $parent;
  public $functions;
  public $globals;

  public static function of(
    string $path,
    string $namespace,
     array $uses,
    string $name,
    string $parent,
     array $functions,
    string $globals
  ): Test {
    $data = new Test;
    $data->path = $path;
    $data->namespace = $namespace;
    $data->uses = $uses;
    $data->name = $name;
    $data->parent = $parent;
    $data->functions = $functions;
    $data->globals = $globals;
    return $data;
  }
}
