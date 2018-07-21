<?php
namespace Wn\Mate;

use Tarsana\Functional as F;

class CommonTest extends \Wn\Mate\Classes\TestCase
{
  function test_alias() {
    $this->assertEquals(alias('parse_file'), 'Wn\Mate\parse_file');
  }

  function test_codes_from_description() {
    $description = "some text\n```php\nfirst code\n```\nother text\n```php\nother\ncode\n```";
    $this->assertEquals(codes_from_description($description), [
      "first code",
      "other\ncode"
    ]);
  }

  function test_split_code() {
    $code = "\$n = 5;\n\$add = function (\$a) {\n  return \$a + 1;\n};\n\$y = \$add(\$n);";
    
    $this->assertEquals(split_code($code), [
      '$n = 5;',
      "\$add = function (\$a) {\n  return \$a + 1;\n};",
      '$y = $add($n);'
    ]);
  }
}

