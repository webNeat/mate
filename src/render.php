<?php
/**
 * @namespace Wn\Mate
 * @use Tarsana\Functional as F
 */
namespace Wn\Mate;

use Tarsana\Functional as F;

function render_test(Test $t): string {
  return F\join("\n", [
    "<?php",
    $t->namespace ? "namespace {$t->namespace};\n" : "",
    F\join("\n", F\map(function($use) {
      return "use {$use};";
    }, $t->uses)),
    "",
    "class {$t->name} extends {$t->parent}\n{",
    F\s($t->functions)
      ->map(F\pipe(alias('render_function_test'), alias('indent')))
      ->filter(F\pipe('trim', F\notEq('')))
      ->join("\n\n")
      ->result(),
    "}",
    "",
    $t->globals
  ]);
}

function render_doc(Doc $doc): string {
  return F\join("\n", [
    "# {$doc->title}\n",
    $doc->header . "\n",
    render_doc_contents($doc) . "\n",
    F\join("\n\n", F\map(alias('render_type_doc'), $doc->types)),
    F\join("\n\n", F\map(alias('render_function_doc'), $doc->functions))
  ]);
}

function render_function_test(FunctionTest $fn): string {
  if (empty(trim($fn->code)))
    return "";
  $code = indent($fn->code);
  return "function {$fn->name}() {\n{$code}\n}";
}

function indent($text) {
  return F\s($text)
    ->split("\n")
    ->map(F\prepend("  "))
    ->join("\n")
    ->result();
}

function render_doc_contents(Doc $doc): string {
  return F\s($doc->types)
    ->concat($doc->functions)
    ->map(function($a) {
      $url = F\snakeCase('_', $a->name);
      return "- [{$a->name}](#{$url})";
    })
    ->join("\n")
    ->result();
}

function render_type_doc(TypeDoc $t): string {
  $fields = '';
  if (count($t->fields) > 0) {
    $fields = F\join("\n", [
      "```php\n{",
      F\join(",\n", F\map(function($field) {
        return "  {$field->type} {$field->name}";
      }, $t->fields)),
      "}\n```"
    ]);
  }
  return F\join("\n", [
    "# {$t->name}",
    $t->description,
    $fields
  ]);
}

function render_function_doc(FunctionDoc $fn): string {
  return F\join("\n", [
    "# {$fn->name}",
    "```php",
    $fn->signature,
    "```",
    $fn->description,
    ($fn->example ? "```php\n{$fn->example}\n```" : "")
  ]);
}
