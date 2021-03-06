<?php
namespace Wn\Mate;

use Tarsana\Functional as F;

class TestTest extends \Wn\Mate\Classes\TestCase
{
  function test_make_test() {
    $module = Module::of(
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
           Parameter::of('$name', 'string', ''),
           Parameter::of('$age', 'int', '')
         ]
       )
     ],
     [
       FunctionBlock::of(
         'increment_age',
         "Makes a person older\n```php\nincrement_age(); // is working\n```",
         [
           Parameter::of('$person', 'Person', ''),
         ],
         'Person'
       )
     ]
    );
    
    $config = Config::of([]);
    $config->srcDir = '/path/to/src';
    $config->testsDir = '/path/to/tests';
    
    $this->assertEquals(make_test($config, $module), Test::of(
      '/path/to/tests/AwesomeNameTest.php',
     'My\Namespace',
     [
       'Tarsana\Functional as F',
       'Other\Library\Class'
     ],
     'AwesomeNameTest',
     '\Wn\Mate\Classes\TestCase',
      [
        FunctionTest::of(
          'test_increment_age',
          'increment_age(); // is working'
        )
      ],
      ''
    ));
  }
}

