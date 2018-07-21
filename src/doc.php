<?php
/**
 * @mate
 * @namespace Wn\Mate
 * @use Tarsana\Functional as F
 */
namespace Wn\Mate;

use Tarsana\Functional as F;

/**
 * Make a `Doc` from a module.
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
 *      ],
 *      []
 *    )
 *  ],
 *  [
 *    FunctionBlock::of(
 *      'increment_age',
 *      'Makes a person older',
 *      [
 *        Parameter::of('$person', 'Person', ''),
 *      ],
 *      'Person',
 *      []
 *    )
 *  ]
 * );
 *
 * $config = Config::of([]);
 * $config->srcDir = '/path/to/src';
 * $config->docsDir = '/path/to/docs';
 *
 * make_doc($config, $module, true); //=> Doc::of(
 *   '/path/to/docs/awesome-name.md',
 *   'awesome-name',
 *   'An awesome group of functions',
 *   [
 *     TypeDoc::of(
 *      'Person',
 *      'A person\'s type',
 *      [
 *        Parameter::of('$name', 'string', ''),
 *        Parameter::of('$age', 'int', '')
 *      ]
 *     )
 *   ],
 *   [
 *     FunctionDoc::of(
 *       'increment_age',
 *       'Makes a person older',
 *       'function increment_age(Person $person) : Person',
 *       ''
 *     )
 *   ]
 * )
 *
 * make_doc($config, $module); //=> Doc::of(
 *   '/path/to/docs/awesome-name.md',
 *   'awesome-name',
 *   'An awesome group of functions',
 *   [],
 *   [
 *     FunctionDoc::of(
 *       'increment_age',
 *       'Makes a person older',
 *       'function increment_age(Person $person) : Person',
 *       ''
 *     )
 *   ]
 * )
 *
 * $config->srcDir = '/other/src/path';
 * make_doc($config, $module); // throws "The module path '/path/to/src/awesome-name.php' does not start with the configured source path '/other/src/path', please use a config file to customize this value."
 * ```
 *
 * @function make_doc
 * @param  Config $config
 * @param  Module $module
 * @param  bool   $withTypes
 * @return Doc
 */
function make_doc(Config $config, Module $module, bool $withTypes = false): Doc {
  if (!F\startsWith($config->srcDir, $module->path))
    throw new \Exception(
      "The module path '{$module->path}' does not start with the ".
      "configured source path '{$config->srcDir}', ".
      "please use a config file to customize this value."
    );
  $title = basename($module->path, '.php');
  $path = F\s($module->path)
    ->remove(F\length($config->srcDir))
    ->prepend($config->docsDir)
    ->then('dirname')
    ->append('/'.$title.'.md')
    ->result();
  $header = $module->description;
  $types = $withTypes ? F\s($module->types)
    ->map(alias('make_type_doc'))
    ->result() : [];
  $functions = F\s($module->functions)
    ->map(alias('make_function_doc'))
    ->result();
  return Doc::of($path, $title, $header, $types, $functions);
}

function make_type_doc(TypeBlock $t): TypeDoc {
  return TypeDoc::of($t->name, $t->description, $t->fields);
}

function make_function_doc(FunctionBlock $fn): FunctionDoc {
  $args = F\join(", ", F\map(function($arg) {
    return $arg->type . ' ' . $arg->name;
  }, $fn->args));

  $description = trim(description_without_codes($fn->description));
  $signature = "function {$fn->name}({$args}) : {$fn->returnType}";
  $example = example_from_description($fn->description);

  return FunctionDoc::of($fn->name, $description, $signature, $example);
}

function description_without_codes(string $description): string {
  return F\s($description)
    ->split("\n")
    ->reduce(function($result, $line) {
      if (trim($line) === '```php')
        $result->inCode = true;
      else if (!$result->inCode)
        $result->description .= "\n" . $line;
      else if ($result->inCode && trim($line) === '```')
        $result->inCode = false;
      return $result;
    }, (object) ['inCode' => false, 'description' => ''])
    ->get('description')
    ->result();
}

function example_from_description(string $description): string {
  $code = F\head(codes_from_description($description));
  if (! $code)
    return '';
  return F\s(split_code($code))
    ->map(function($chunk) {
      if (F\contains('; //=> ', $chunk)) {
        $parts = F\split('; //=> ', $chunk);
        $lines = F\split("\n", $parts[1]);
        $lines = F\s($lines)
          ->tail()
          ->map(F\prepend("// "))
          ->prepend(F\head($lines))
          ->join("\n")
          ->result();
        $chunk = F\join('; //=> ', [$parts[0], $lines]);
      }
      return $chunk;
    })
    ->join("\n")
    ->result();
}
