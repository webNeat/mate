# parse

Functions to parse source files.

- [make_module](#make_module)
- [blocks_from_content](#blocks_from_content)
- [comments](#comments)
- [block_from_comment](#block_from_comment)
- [lines_of_comment](#lines_of_comment)
- [description_from_lines](#description_from_lines)
- [tags_from_lines](#tags_from_lines)
- [block_maker](#block_maker)
- [make_type_block](#make_type_block)
- [make_unknown_block](#make_unknown_block)
- [make_function_block](#make_function_block)
- [tag_values](#tag_values)
- [make_parameter](#make_parameter)
- [tag_from_line](#tag_from_line)


# make_module
```php
function make_module(Module $file) : Module
```

Creates a module from a path and source code.


# blocks_from_content
```php
function blocks_from_content(string $content) : array<Block>
```

Returns an array of `Block`s from a content.


# comments
```php
function comments(string $text) : array
```

Gets the list of PHPDoc comments from a source code.


# block_from_comment
```php
function block_from_comment(string $comment) : Block
```

Parses a comment and returns a Block.


# lines_of_comment
```php
function lines_of_comment(string $comment) : array<string>
```

Gets lines of the content of a comment.


# description_from_lines
```php
function description_from_lines(array<string> $lines) : string
```

Returns the description text from an array of
lines by skipping lines which starts with `@`.

```php
description_from_lines([
  'First line here',
  '@tag value',
  'Second line',
  'Third line',
  '@other tag',
  'Final line'
]); //=> "First line here\nSecond line\nThird line\nFinal line"
```

# tags_from_lines
```php
function tags_from_lines(array<string> $lines) : array<Tag>
```

Creates a list of tags from a list of lines by
skipping lines which does not start by `@`.
```php
tags_from_lines([
  '@name value',
  '  @tag value with spaces',
  'Random line 1',
  '@tag-without-value  ',
  ' @spaces    are   trimed   ',
  'Random line 2'
]); //=> [
//   Tag::of('name', 'value'),
//   Tag::of('tag', 'value with spaces'),
//   Tag::of('tag-without-value', ''),
//   Tag::of('spaces', 'are   trimed')
// ]
```

# block_maker
```php
function block_maker(array<Tag> $tags) : string
```

Gets the `make_{$type}_block` function corresponding
to the given tags or empty string if none found.

```php
block_maker([
  Tag::of('type', 'MyAwesomeType'),
  Tag::of('field', 'string $name'),
  //...
]); //=> alias('make_type_block')
block_maker([
  Tag::of('function', 'my_awesome_function'),
  Tag::of('return', 'array<int>'),
  //...
]); //=> alias('make_function_block')
block_maker([
  Tag::of('foo', 'bar'),
  //...
]); //=> alias('make_unknown_block')
block_maker([]); //=> alias('make_unknown_block')
```

# make_type_block
```php
function make_type_block(string $description, array $tags) : TypeBlock
```

Creates a TypeBlock from the given description and tags.

```php
make_type_block('a special type.', [
  Tag::of('type', 'Special'),
  Tag::of('field', 'string $name'),
  Tag::of('foo', 'bar baz'),
  Tag::of('field', 'array<Special> $related list of related objects'),
]); //=> TypeBlock::of('Special', 'a special type.', [
//   Parameter::of('$name', 'string', ''),
//   Parameter::of('$related', 'array<Special>', 'list of related objects')
// ], [
//   Tag::of('foo', 'bar baz')
// ])
```

# make_unknown_block
```php
function make_unknown_block(string $description, array $tags) : UnknownBlock
```

Creates an UnknownBlock from the given description and tags.


# make_function_block
```php
function make_function_block(string $description, array $tags) : FunctionBlock
```

Creates a FunctionBlock from the given description and tags.

```php
make_function_block('a small function.', [
  Tag::of('function', 'do_staff'),
  Tag::of('param', 'string $name'),
  Tag::of('foo', 'bar baz'),
  Tag::of('param', '((string): string) $fn converting function'),
  Tag::of('return', 'array<string>  '),
]); //=> FunctionBlock::of('do_staff', 'a small function.', [
//   Parameter::of('$name', 'string', ''),
//   Parameter::of('$fn', '((string): string)', 'converting function')
// ], 'array<string>', [
//   Tag::of('foo', 'bar baz')
// ])
```

# tag_values
```php
function tag_values(string $name, Block $block) : array<Tag>
```

Gets array of specific tag values from a block.


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
function tag_from_line(string $line) : Tag
```

Converts a comment line `@xxx yyyy` to a
tag with name `xxx` and content `yyy`.
```php
tag_from_line('@tag value'); //=> Tag::of('tag', 'value')
tag_from_line('@tag    value is here'); //=> Tag::of('tag', 'value is here')
tag_from_line('@tag-with-dashes value  spaces'); //=> Tag::of('tag-with-dashes', 'value  spaces')
```