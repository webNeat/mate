<?php
/**
 * @mate
 * @namespace Wn\Mate
 * @use Tarsana\Functional as F
 */
namespace Wn\Mate;

use Tarsana\Functional as F;

/**
 * Make a `Test` from a module.
 * ```php
 * $module = Module::of(
 *  '/path/to/src/awesome-name.php',
 *  'An awesome group of functions',
 *  'My\Namespace',
 *  [
 *    'Tarsana\Functional as F',
 *    'Other\Library\Class'
 *  ],
 *  [
 *    TypeBlock::of(
 *      'Person',
 *      'A person\'s type',
 *      [
 *        Parameter::of('$name', 'string', ''),
 *        Parameter::of('$age', 'int', '')
 *      ]
 *    )
 *  ],
 *  [
 *    FunctionBlock::of(
 *      'increment_age',
 *      "Makes a person older\n```php\nincrement_age(); // is working\n```",
 *      [
 *        Parameter::of('$person', 'Person', ''),
 *      ],
 *      'Person'
 *    )
 *  ]
 * );
 *
 * $config = Config::of([]);
 * $config->srcDir = '/path/to/src';
 * $config->testsDir = '/path/to/tests';
 *
 * make_test($config, $module); //=> Test::of(
 *   '/path/to/tests/AwesomeNameTest.php',
 *  'My\Namespace',
 *  [
 *    'Tarsana\Functional as F',
 *    'Other\Library\Class'
 *  ],
 *  'AwesomeNameTest',
 *  '\Wn\Mate\Classes\TestCase',
 *   [
 *     FunctionTest::of(
 *       'test_increment_age',
 *       'increment_age(); // is working'
 *     )
 *   ],
 *   ''
 * )
 * ```
 *
 * @function make_test
 * @param  Config $config
 * @param  Module $module
 * @return Test
 */
function make_test(Config $config, Module $module): Test {
  if (!F\startsWith($config->srcDir, $module->path))
    throw new \Exception(
      "The module path does not start with the ".
      "configured source path '{$config->srcDir}', ".
      "please use a config file to customize this value."
    );
  $namespace = $module->namespace;
  $uses = $module->uses;
  $name = F\s(basename($module->path, '.php'))
    ->append('-test')
    ->camelCase()
    ->then('ucwords')
    ->result();
  $path = F\s($module->path)
    ->remove(F\length($config->srcDir))
    ->prepend($config->testsDir)
    ->then('dirname')
    ->append('/'.$name.'.php')
    ->result();
  $parent = $config->testCaseClass;
  $functions = F\s($module->functions)
    ->map(alias('make_function_test'))
    ->result();
  $globals = global_code($module);
  return Test::of($path, $namespace, $uses, $name, $parent, $functions, $globals);
}

function make_function_test(FunctionBlock $fn): FunctionTest {
  $name = 'test_' . $fn->name;
  $code = F\s($fn->description)
    ->then(alias('codes_from_description'))
    ->join("\n")
    ->then(alias('split_code'))
    ->map(alias('make_assertion'))
    ->join("\n")
    ->result();
  return FunctionTest::of($name, $code);
}

function global_code(Module $module): string {
  return F\s($module->functions)
    ->map(F\get('description'))
    ->append($module->description)
    ->chain(alias('codes_from_description'))
    ->chain(alias('split_code'))
    ->filter(F\any(F\startsWith('function '), F\startsWith('class ')))
    ->join("\n\n")
    ->result();
}

function make_assertion(string $stmnt): string {
  if (F\contains('; //=> ', $stmnt)) {
    $parts = F\split('; //=> ', $stmnt);
    return make_equal_assertion(...$parts);
  }
  if (F\contains('; // throws ', $stmnt)) {
    $parts = F\split('; // throws ', $stmnt);
    return make_throw_assertion(...$parts);
  }
  return $stmnt;
}

function make_equal_assertion(string $a, string $b): string {
  return "\$this->assertEquals({$a}, {$b});";
}

function make_throw_assertion(string $fn, string $msg): string {
  $vars = F\match('/\$[a-zA-Z0-9_]+/', $fn);
  $use = '';
  if (count($vars) > 0)
    $use = 'use(' . F\join(', ', $vars) . ') ';
  return "\$this->assertThrows(function() {$use}{\n\t{$fn};\n},\n{$msg});";
}
