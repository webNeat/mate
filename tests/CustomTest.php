<?php
namespace Wn\Mate;

class CustomTest extends \PHPUnit\Framework\TestCase
{
  public function test_split_code() {
    $parts = [
"\$module = Module::of(
 '/path/to/src/awesome-name.php',
 'An awesome group of functions',
 'My\Namespace',
 [
   'Tarsana\Functional as F',
   'Other\Library\Class'
 ],
 [
   TypeBlock::of(
     'Person',
     'A person\'s type',
     [
       Parameter::of('\$name', 'string', ''),
       Parameter::of('\$age', 'int', '')
     ],
     []
   )
 ],
 [
   FunctionBlock::of(
     'increment_age',
     'Makes a person older',
     [
       Parameter::of('\$person', 'Person', ''),
     ],
     'Person',
     []
   )
 ]
);",
"\$config = Config::of([
  'srcDir' => '/path/to/src',
  'docsDir' => '/path/to/docs',
  'testsDir' => '/path/to/tests'
]);",
"make_doc(\$config, \$module, true); //=> Doc::of(
  '/path/to/docs/awesome-name.php',
  'AwesomeName',
  'An awesome group of functions',
  [
    TypeDoc::of(
     'Person',
     'A person\'s type',
     [
       Parameter::of('\$name', 'string', ''),
       Parameter::of('\$age', 'int', '')
     ]
    )
  ],
  [
    'increment_age',
    'Makes a person older',
    'function increment_age(Person \$person): Person',
    ''
  ]
);",
"make_doc(\$config, \$module); //=> Doc::of(
  '/path/to/docs/awesome-name.php',
  'AwesomeName',
  'An awesome group of functions',
  [],
  [
    'increment_age',
    'Makes a person older',
    'function increment_age(Person \$person): Person',
    ''
  ]
)"

];
    $this->assertEquals(split_code(implode("\n", $parts)), $parts);
  }

}
