<?php
/**
 * Functions to parse source files.
 * @mate
 * @namespace Wn\Mate
 * @use Tarsana\Functional as F
*/
namespace Wn\Mate;

use Tarsana\Functional as F;

/**
 * Creates a module from a path and source code.
 *
 * @function make_module
 * @param Module $file
 * @return Module | null
 */
function make_module(File $file) {
  $blocks = blocks_from_content($file->content);
  if (empty($blocks) || empty(tag_values('mate', F\head($blocks)))) {
    return null;
  }

  $info = F\head($blocks);
  $description = $info->description;
  $namespace = F\head(tag_values('namespace', $info)) ?: '';
  $uses = tag_values('use', $info);
  $types = F\filter(F\pipe(F\get('type'), F\eq('type')), $blocks);
  $functions = F\filter(F\pipe(F\get('type'), F\eq('function')), $blocks);

  return Module::of(
    $file->path,
    $description,
    $namespace,
    $uses,
    $types,
    $functions
  );
}

/**
 * Returns an array of `Block`s from a content.
 *
 * @function blocks_from_content
 * @param  string $content
 * @return array<Block>
 */
function blocks_from_content(string $content): array {
  return F\s(comments_from_code($content))
    ->map(alias('block_from_comment'))
    ->filter(F\notEq(null))
    ->result();
}

/**
 * Gets the list of PHPDoc comments from a source code.
 *
 * @function comments
 * @param  string $text
 * @return array
 * @signature String -> [String]
 */
function comments_from_code(string $text): array {
  return F\pipe(
    'token_get_all',
    F\filter(F\pipe(F\get(0), F\eq(T_DOC_COMMENT))),
    F\map(F\get(1)),
    F\filter(F\notEq(null))
  )($text);
}

/**
 * Parses a comment and returns a Block.
 *
 * @function block_from_comment
 * @param  string $comment
 * @return Block | null
 */
function block_from_comment(string $comment) {
  $lines = lines_of_comment($comment);
  $description = description_from_lines($lines);
  $tags = tags_from_lines($lines);
  $make = block_maker($tags);
  return $make($description, $tags);
}

/**
 * Gets lines of the content of a comment.
 *
 * @function lines_of_comment
 * @param  string $comment
 * @return array<string>
 */
function lines_of_comment(string $comment): array {
  return F\pipe(
    F\split("\n"),
    F\remove(1),            // remove '/**'
    F\remove(-1),           // remove '*/'
    F\map(function($line) { // remove '* '
      return F\remove(2, trim($line));
    })
  )($comment);
}

/**
 * Returns the description text from an array of
 * lines by skipping lines which starts with `@`.
 *
 * ```php
 * description_from_lines([
 *   'First line here',
 *   '@tag value',
 *   'Second line',
 *   'Third line',
 *   '@other tag',
 *   'Final line'
 * ]); //=> "First line here\nSecond line\nThird line\nFinal line"
 * ```
 *
 * @function description_from_lines
 * @param  array<string> $lines
 * @return string
 */
function description_from_lines(array $lines): string {
  return F\pipe(
    F\filter(F\pipe(       // take lines not starting with '@'
      F\startsWith('@'),
      F\eq(false)
    )),
    F\join("\n"),
    'trim'
  )($lines);
}

/**
 * Creates a list of tags from a list of lines by
 * skipping lines which does not start by `@`.
 * ```php
 * tags_from_lines([
 *   '@name value',
 *   '  @tag value with spaces',
 *   'Random line 1',
 *   '@tag-without-value  ',
 *   ' @spaces    are   trimed   ',
 *   'Random line 2'
 * ]); //=> [
 *   Tag::of('name', 'value'),
 *   Tag::of('tag', 'value with spaces'),
 *   Tag::of('tag-without-value', ''),
 *   Tag::of('spaces', 'are   trimed')
 * ]
 * ```
 *
 * @function tags_from_lines
 * @param  array<string>  $lines
 * @return array<Tag>
 */
function tags_from_lines(array $lines): array {
  return F\pipe(
    F\filter(F\pipe(              // take lines starting with '@'
      'trim',
      F\startsWith('@'),
      F\eq(true)
    )),
    F\map(alias('tag_from_line'))
  )($lines);
}

/**
 * Gets the `make_{$type}_block` function corresponding
 * to the given tags or empty string if none found.
 *
 * ```php
 * block_maker([
 *   Tag::of('type', 'MyAwesomeType'),
 *   Tag::of('field', 'string $name'),
 *   //...
 * ]); //=> alias('make_type_block')
 * block_maker([
 *   Tag::of('function', 'my_awesome_function'),
 *   Tag::of('return', 'array<int>'),
 *   //...
 * ]); //=> alias('make_function_block')
 * block_maker([
 *   Tag::of('foo', 'bar'),
 *   //...
 * ]); //=> alias('make_unknown_block')
 * block_maker([]); //=> alias('make_unknown_block')
 * ```
 * @function block_maker
 * @param  array<Tag> $tags
 * @return string
 */
function block_maker(array $tags): string {
  $types = ['type', 'function'];

  $type = F\pipe(
    F\map(F\get('name')),
    F\find(F\apply(
      "Tarsana\\Functional\\any",
      F\map(F\eq(), $types)
    ))
  )($tags) ?: 'unknown';

  return alias("make_{$type}_block");
}

/**
 * Creates a TypeBlock from the given description and tags.
 * ```php
 * make_type_block('a special type.', [
 *   Tag::of('type', 'Special'),
 *   Tag::of('field', 'string $name'),
 *   Tag::of('foo', 'bar baz'),
 *   Tag::of('field', 'array<Special> $related list of related objects'),
 * ]); //=> TypeBlock::of('Special', 'a special type.', [
 *   Parameter::of('$name', 'string', ''),
 *   Parameter::of('$related', 'array<Special>', 'list of related objects')
 * ], [
 *   Tag::of('foo', 'bar baz')
 * ])
 * ```
 *
 * ```php
 * make_type_block('...', []); //=> TypeBlock::of('', '...', [], [])
 * make_type_block('...', [
 *   Tag::of('field', 'string $name'),
 * ]); //=> TypeBlock::of('', '...', [
 *   Parameter::of('$name', 'string', ''),
 * ], [])
 * ```
 * @function make_type_block
 * @param  string $description
 * @param  array  $tags
 * @return TypeBlock
 */
function make_type_block(string $description, array $tags): TypeBlock {
  $block = TypeBlock::of('', $description, [], []);
  return F\reduce(function($block, $tag) {
    if ($tag->name === 'type')
      $block->name = $tag->content;
    else if ($tag->name === 'field')
      $block->fields[] = make_parameter($tag->content);
    else
      $block->tags[] = $tag;
    return $block;
  }, $block, $tags);
}

/**
 * Creates an UnknownBlock from the given description and tags.
 * @function make_unknown_block
 * @param  string $description
 * @param  array  $tags
 * @return UnknownBlock
 */
function make_unknown_block(string $description, array $tags): UnknownBlock {
  return UnknownBlock::of($description, $tags);
}

/**
 * Creates a FunctionBlock from the given description and tags.
 * ```php
 * make_function_block('a small function.', [
 *   Tag::of('function', 'do_staff'),
 *   Tag::of('param', 'string $name'),
 *   Tag::of('foo', 'bar baz'),
 *   Tag::of('param', '((string): string) $fn converting function'),
 *   Tag::of('return', 'array<string>  '),
 * ]); //=> FunctionBlock::of('do_staff', 'a small function.', [
 *   Parameter::of('$name', 'string', ''),
 *   Parameter::of('$fn', '((string): string)', 'converting function')
 * ], 'array<string>', [
 *   Tag::of('foo', 'bar baz')
 * ])
 * ```
 *
 * ```php
 * make_function_block('...', []); //=> FunctionBlock::of('', '...', [], '', [])
 * ```
 * @function make_function_block
 * @param  string $description
 * @param  array  $tags
 * @return FunctionBlock
 */
function make_function_block(string $description, array $tags): FunctionBlock {
  $block = FunctionBlock::of('', $description, [], '', []);
  return F\reduce(function($block, $tag) {
    if ($tag->name === 'function')
      $block->name = $tag->content;
    else if ($tag->name === 'param')
      $block->args[] = make_parameter($tag->content);
    else if ($tag->name === 'return')
      $block->returnType = F\head(F\chunks('<>()', ' ', $tag->content));
    else
      $block->tags[] = $tag;
    return $block;
  }, $block, $tags);
}

/**
 * Gets array of specific tag values from a block.
 *
 * @function tag_values
 * @param  string $name
 * @param  Block  $block
 * @return array<Tag>
 */
function tag_values(string $name, Block $block): array {
  return F\s($block->tags)
    ->filter(F\pipe(F\get('name'), F\eq($name)))
    ->map(F\get('content'))
    ->result();
}

/**
 * Creates a Parameter from the content of `@param` or `@field` tags.
 * ```php
 * make_parameter(' string   $name  ...'); //=> Parameter::of('$name', 'string', '...')
 * make_parameter('array< T > $fns '); //=> Parameter::of('$fns', 'array< T >')
 * make_parameter('((int, int): int) $fn '); //=> Parameter::of('$fn', '((int, int): int)')
 * ```
 * @function make_parameter
 * @param  string $text
 * @return Parameter
 */
function make_parameter(string $text): Parameter {
  $parts = F\chunks('()<>', ' ', trim($text));
  $type = F\head($parts);
  $parts = F\removeWhile(F\eq(''), F\tail($parts));
  $name = F\head($parts);
  $description = trim(F\join(' ', F\tail($parts)));
  return Parameter::of($name, $type, $description);
}

/**
 * Converts a comment line `@xxx yyyy` to a
 * tag with name `xxx` and content `yyy`.
 * ```php
 * tag_from_line('@tag value'); //=> Tag::of('tag', 'value')
 * tag_from_line('@tag    value is here'); //=> Tag::of('tag', 'value is here')
 * tag_from_line('@tag-with-dashes value  spaces'); //=> Tag::of('tag-with-dashes', 'value  spaces')
 * ```
 * @function tag_from_line
 * @param  string $line
 * @return Tag
 */
function tag_from_line(string $line): Tag {
  $line = trim($line);
  $index = F\indexOf(' ', $line);
  if ($index == -1) {
    $name = F\remove(1, $line);                   // remove '@'
    $content = '';
  } else {
    $name = F\remove(1, F\take($index, $line));   // take until first space then remove '@'
    $content = trim(F\remove($index + 1, $line)); // remove until first space included
  }
  return Tag::of($name, $content);
}
