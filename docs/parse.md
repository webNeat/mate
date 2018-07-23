# parse

Functions to parse source files.

- [make_module](#make_module)
- [blocks_from_content](#blocks_from_content)
- [block_from_comment](#block_from_comment)
- [make_type_block](#make_type_block)
- [make_function_block](#make_function_block)
- [make_parameter](#make_parameter)
- [tag_from_line](#tag_from_line)


# make_module
```php
function make_module(Module $file) : Module
```
Creates a module from a file.


# blocks_from_content
```php
function blocks_from_content(string $content) : array<Block>
```
Returns an array of `Block`s from a content.


# block_from_comment
```php
function block_from_comment(string $comment) : Block
```
Parses a comment and returns a `Block`.


# make_type_block
```php
function make_type_block(UnknownBlock $block) : Block
```
Makes a `TypeBlock` from an `Block` if it has a `@type` tag.
The `Block` is returned otherwise.


# make_function_block
```php
function make_function_block(UnknownBlock $block) : Block
```
Makes a `FunctionBlock` from an `UnknownBlock` if it has a `@function` tag.
The `UnknownBlock` is returned otherwise.


# make_parameter
```php
function make_parameter(string $text) : Parameter
```
Creates a Parameter from the content of `@param` or `@field` tags.
```php
make_parameter(' string   $name  ...'); //=> Parameter::of('$name', 'string', '...')
make_parameter('array< T > $fns '); //=> Parameter::of('$fns', 'array< T >')
make_parameter('((int, int): int) $fn '); //=> Parameter::of('$fn', '((int, int): int)')
```

# tag_from_line
```php
function tag_from_line(string $line) : array<string>
```
Converts a comment line `@xxx yyyy zzz` to the pair `['xxx', 'yyyy zzz']`.
```php
tag_from_line('@tag value'); //=> ['tag', 'value']
tag_from_line('@tag    value is here'); //=> ['tag', 'value is here']
tag_from_line('@tag-with-dashes value  spaces'); //=> ['tag-with-dashes', 'value  spaces']
```