<?php
namespace Wn\Mate;

use Tarsana\Functional as F;

class ParseTest extends \Wn\Mate\Classes\TestCase
{
  function test_description_from_lines() {
    $this->assertEquals(description_from_lines([
      'First line here',
      '@tag value',
      'Second line',
      'Third line',
      '@other tag',
      'Final line'
    ]), "First line here\nSecond line\nThird line\nFinal line");
  }

  function test_tags_from_lines() {
    $this->assertEquals(tags_from_lines([
      '@name value',
      '  @tag value with spaces',
      'Random line 1',
      '@tag-without-value  ',
      ' @spaces    are   trimed   ',
      'Random line 2'
    ]), [
      Tag::of('name', 'value'),
      Tag::of('tag', 'value with spaces'),
      Tag::of('tag-without-value', ''),
      Tag::of('spaces', 'are   trimed')
    ]);
  }

  function test_block_maker() {
    $this->assertEquals(block_maker([
      Tag::of('type', 'MyAwesomeType'),
      Tag::of('field', 'string $name'),
      //...
    ]), alias('make_type_block'));
    $this->assertEquals(block_maker([
      Tag::of('function', 'my_awesome_function'),
      Tag::of('return', 'array<int>'),
      //...
    ]), alias('make_function_block'));
    $this->assertEquals(block_maker([
      Tag::of('foo', 'bar'),
      //...
    ]), alias('make_unknown_block'));
    $this->assertEquals(block_maker([]), alias('make_unknown_block'));
  }

  function test_make_type_block() {
    $this->assertEquals(make_type_block('a special type.', [
      Tag::of('type', 'Special'),
      Tag::of('field', 'string $name'),
      Tag::of('foo', 'bar baz'),
      Tag::of('field', 'array<Special> $related list of related objects'),
    ]), TypeBlock::of('Special', 'a special type.', [
      Parameter::of('$name', 'string', ''),
      Parameter::of('$related', 'array<Special>', 'list of related objects')
    ], [
      Tag::of('foo', 'bar baz')
    ]));
    $this->assertEquals(make_type_block('...', []), TypeBlock::of('', '...', [], []));
    $this->assertEquals(make_type_block('...', [
      Tag::of('field', 'string $name'),
    ]), TypeBlock::of('', '...', [
      Parameter::of('$name', 'string', ''),
    ], []));
  }

  function test_make_function_block() {
    $this->assertEquals(make_function_block('a small function.', [
      Tag::of('function', 'do_staff'),
      Tag::of('param', 'string $name'),
      Tag::of('foo', 'bar baz'),
      Tag::of('param', '((string): string) $fn converting function'),
      Tag::of('return', 'array<string>  '),
    ]), FunctionBlock::of('do_staff', 'a small function.', [
      Parameter::of('$name', 'string', ''),
      Parameter::of('$fn', '((string): string)', 'converting function')
    ], 'array<string>', [
      Tag::of('foo', 'bar baz')
    ]));
    $this->assertEquals(make_function_block('...', []), FunctionBlock::of('', '...', [], '', []));
  }

  function test_make_parameter() {
    $this->assertEquals(make_parameter(' string   $name  ...'), Parameter::of('$name', 'string', '...'));
    $this->assertEquals(make_parameter('array< T > $fns '), Parameter::of('$fns', 'array< T >'));
    $this->assertEquals(make_parameter('((int, int): int) $fn '), Parameter::of('$fn', '((int, int): int)'));
  }

  function test_tag_from_line() {
    $this->assertEquals(tag_from_line('@tag value'), Tag::of('tag', 'value'));
    $this->assertEquals(tag_from_line('@tag    value is here'), Tag::of('tag', 'value is here'));
    $this->assertEquals(tag_from_line('@tag-with-dashes value  spaces'), Tag::of('tag-with-dashes', 'value  spaces'));
  }
}

