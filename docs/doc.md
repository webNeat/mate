# doc

/**
*/

- [make_doc](#make_doc)


# make_doc
```php
function make_doc(Config $config, Module $module, bool $withTypes) : Doc
```
/**
Make a `Doc` from a module.

*/
```php
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
     'Makes a person older',
     [
       Parameter::of('$person', 'Person', ''),
     ],
     'Person'
   )
 ]
);

$config = Config::of([]);
$config->srcDir = '/path/to/src';
$config->docsDir = '/path/to/docs';

make_doc($config, $module, true); //=> Doc::of(
//   '/path/to/docs/awesome-name.md',
//   'awesome-name',
//   'An awesome group of functions',
//   [
//     TypeDoc::of(
//      'Person',
//      'A person\'s type',
//      [
//        Parameter::of('$name', 'string', ''),
//        Parameter::of('$age', 'int', '')
//      ]
//     )
//   ],
//   [
//     FunctionDoc::of(
//       'increment_age',
//       'Makes a person older',
//       'function increment_age(Person $person) : Person',
//       ''
//     )
//   ]
// )

make_doc($config, $module); //=> Doc::of(
//   '/path/to/docs/awesome-name.md',
//   'awesome-name',
//   'An awesome group of functions',
//   [],
//   [
//     FunctionDoc::of(
//       'increment_age',
//       'Makes a person older',
//       'function increment_age(Person $person) : Person',
//       ''
//     )
//   ]
// )

$config->srcDir = '/other/src/path';
make_doc($config, $module); // throws "The module path '/path/to/src/awesome-name.php' does not start with the configured source path '/other/src/path', please use a config file to customize this value."
```