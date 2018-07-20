<?php
namespace Wn\Mate;

use Tarsana\Functional as F;

function run(string $configPath = '', bool $runTests, bool $watch) {
  $config = Config::of(load_json($configPath));
  $files = F\map(alias('load_file'), source_paths($config->srcDir));
  $hashes = load_json('mate.lock');
  $modules = F\s($files)
    ->map(alias('make_module'))
    ->filter(F\notEq(null))
    ->map(function($module) {
      return [$module->path, $module];
    })
    ->fromPairs()
    ->result();

  foreach (changed_modules($hashes, F\values($modules)) as $module) {
    write_test($config, $module);
    write_doc($config, $module);
  }
  write_types($config, $hashes, F\values($modules));

  if ($runTests) run_tests();

  save_json('mate.lock', $hashes);
}

function changed_modules(array &$hashes, array $modules): array {
  return F\filter(function($module) use(&$hashes) {
    $path = $module->path;
    $hash = hash_module($module);
    if (empty($hashes[$path]) || $hash != $hashes[$path]) {
      $hashes[$path] = $hash;
      return true;
    }
    return false;
  }, $modules);
}

function write_test(Config $config, Module $module) {
  if (!empty($module->functions)) {
    $test = make_test($config, $module);
    write_file(render_test($test), $test->path);
  }
}

function write_doc(Config $config, Module $module) {
  if (!empty($module->types) || !empty($module->functions)) {
    $doc = make_doc($config, $module);
    write_file(render_doc($doc), $doc->path);
  }
}

function write_types(Config $config, array &$hashes, array $modules) {
  $types = F\chain(F\get('types'), $modules);
  if (empty($types)) return;
  $path = $config->srcDir . '/_types.php';
  $module = Module::of($path, '', '', [], $types, []);
  $hash = hash_module($module);
  if (empty($hashes[$path]) || $hashes[$path] != $hash) {
    $hashes[$path] = $hash;
    $doc = make_doc($config, $module, true);
    write_file(render_doc($doc), $doc->path);
  }
}

function hash_module(Module $module): string {
  return md5(json_encode($module));
}

function run_tests() {
  system('./vendor/bin/phpunit');
}
