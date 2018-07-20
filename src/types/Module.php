<?php
namespace Wn\Mate;

/**
 * @mate
 * @type Module
 * @field string $path
 * @field string $description
 * @field string $namespace
 * @field array<string> $uses
 * @field array<TypeBlock> $types
 * @field array<FunctionBlock> $functions
 */
class Module {
  public $path;
  public $description;
  public $namespace;
  public $uses;
  public $types;
  public $functions;

  public static function of(
    string $path,
    string $description,
    string $namespace,
     array $uses,
     array $types,
     array $functions
  ): Module {
    $data = new Module;
    $data->path = $path;
    $data->description = $description;
    $data->namespace = $namespace;
    $data->uses = $uses;
    $data->types = $types;
    $data->functions = $functions;
    return $data;
  }
}
