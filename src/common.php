<?php
/**
 * @mate
 * @namespace Wn\Mate
 * @use Tarsana\Functional as F
 */
namespace Wn\Mate;

use Tarsana\Functional as F;

/**
 * Returns the full name of a function within this project.
 * ```php
 * alias('parse_file'); //=> 'Wn\Mate\parse_file'
 * ```
 * @function alias
 * @param  string $name
 * @return string
 */
function alias(string $name): string {
  return "Wn\\Mate\\{$name}";
}

/**
 * Gets an array of code snippets from a text.
 * ```php
 * $description = "some text\n```php\nfirst code\n```\nother text\n```php\nother\ncode\n```";
 * codes_from_description($description); //=> [
 *   "first code",
 *   "other\ncode"
 * ]
 * ```
 * @function codes_from_description
 * @param  string $description
 * @return array<string>
 */
function codes_from_description(string $description): array {
  return F\s($description)
    ->split("\n")
    ->reduce(function($result, $line) {
      if ($result->inCode && trim($line) === '```') {
        $result->codes[] = F\join("\n", $result->lines);
        $result->lines = [];
        $result->inCode = false;
      }
      else if ($result->inCode)
        $result->lines[] = $line;
      else if (trim($line) === '```php')
        $result->inCode = true;
      return $result;
    }, (object) ['inCode' => false, 'codes' => [], 'lines' => []])
    ->get('codes')
    ->result();
}

/**
 * Splits code into multiple statements.
 * ```php
 * $code = "\$n = 5;\n\$add = function (\$a) {\n  return \$a + 1;\n};\n\$y = \$add(\$n);";
 *
 * split_code($code); //=> [
 *   '$n = 5;',
 *   "\$add = function (\$a) {\n  return \$a + 1;\n};",
 *   '$y = $add($n);'
 * ]
 * ```
 * @function split_code
 * @param  string $code
 * @return array
 */
function split_code(string $code): array {
  return F\chunks("\"\"''{}[]()", "\n", $code);
}
