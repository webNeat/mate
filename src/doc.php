<?php
/**
 * @namespace Wn\Mate
 * @use Tarsana\Functional as F
 */
namespace Wn\Mate;

use Tarsana\Functional as F;

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

  $description = description_without_codes($fn->description);
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
