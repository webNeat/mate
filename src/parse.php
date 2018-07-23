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
 * Creates a module from a file.
 *
 * @function make_module
 * @param Module $file
 * @return Module | null
 */
function make_module(File $file) {
  $blocks = blocks_from_content($file->content);
  if (empty($blocks) || empty(F\head($blocks)->tags->mate)) {
    return null;
  }

  $info = F\head($blocks);
  $description = $info->description;
  $namespace = !empty($info->tags->namespace)
    ? F\head($info->tags->namespace)
    : '';
  $uses = !empty($info->tags->use)
    ? $info->tags->use
    : [];
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
  return F\s($content)
    ->match("/\/\*(?:[^*]|(?:\*[^\/]))*\*\//")
    ->map(function($comment) {
      $lines = explode("\n", $comment);
      array_pop($lines);
      array_shift($lines);
      return block_from_comment_lines($lines);
    })
    ->result();
}

/**
 * Parses a comment and returns a `Block`.
 *
 * @function block_from_comment
 * @param  string $comment
 * @return Block | object
 */
function block_from_comment_lines(array $lines) {
  return F\s($lines)
    ->reduce(function($block, $line) {
      $line = trim_comment_line($line);
      if (F\startsWith('@', $line)) {
        $tag = tag_from_line($line);
        if (empty($block->tags->{$tag[0]}))
          $block->tags->{$tag[0]} = [$tag[1]];
        else
          $block->tags->{$tag[0]}[] = $tag[1];
      } else {
        $block->description[] = $line;
      }
      return $block;
    }, (object) ['description' => [], 'tags' => (object) []])
    ->then(function($block) {
      $block = UnknownBlock::of(F\join("\n", $block->description), $block->tags);
      return make_type_block(make_function_block($block));
    })
    ->result();
}

/**
 * Makes a `TypeBlock` from an `Block` if it has a `@type` tag.
 * The `Block` is returned otherwise.
 *
 * @function make_type_block
 * @param  UnknownBlock $block
 * @return Block
 */
function make_type_block(Block $block): Block {
  if (!($block instanceof UnknownBlock) || empty($block->tags->type))
    return $block;
  $name = F\head($block->tags->type);
  $description = $block->description;
  $fields = F\map(
    alias('make_parameter'),
    !empty($block->tags->field) ? $block->tags->field : []
  );

  unset($block->tags->type);
  unset($block->tags->field);

  return TypeBlock::of($name, $description, $fields, $block->tags);
}

/**
 * Makes a `FunctionBlock` from an `UnknownBlock` if it has a `@function` tag.
 * The `UnknownBlock` is returned otherwise.
 *
 * @function make_function_block
 * @param  UnknownBlock $block
 * @return Block
 */
function make_function_block(Block $block): Block {
  if (!($block instanceof UnknownBlock) || empty($block->tags->function))
    return $block;
  $name = F\head($block->tags->function);
  $description = $block->description;
  $args = F\map(
    alias('make_parameter'),
    !empty($block->tags->param) ? $block->tags->param : []
  );
  $returnType = F\head(F\chunks('<>()', ' ', F\head($block->tags->return)));

  unset($block->tags->function);
  unset($block->tags->param);
  unset($block->tags->return);

  return FunctionBlock::of($name, $description, $args, $returnType, $block->tags);
}

/**
 * Removes trailing spaces and the first `* ` part from a comment line.
 *
 * @param  string $line
 * @return string
 */
function trim_comment_line(string $line): string {
  $line = trim($line);
  if (F\length($line) < 2) return '';
  return F\startsWith('* ', $line) ? F\remove(2, $line) : $line;
}

/**
 * Creates a Parameter from the content of `@param` or `@field` tags.
 *
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
 * Converts a comment line `@xxx yyyy zzz` to the pair `['xxx', 'yyyy zzz']`.
 *
 * ```php
 * tag_from_line('@tag value'); //=> ['tag', 'value']
 * tag_from_line('@tag    value is here'); //=> ['tag', 'value is here']
 * tag_from_line('@tag-with-dashes value  spaces'); //=> ['tag-with-dashes', 'value  spaces']
 * ```
 * @function tag_from_line
 * @param  string $line
 * @return array<string>
 */
function tag_from_line(string $line): array {
  $index = F\indexOf(' ', $line);
  if ($index == -1) {
    $name = F\remove(1, $line);                   // remove '@'
    $content = true;
  } else {
    $name = F\remove(1, F\take($index, $line));   // take until first space then remove '@'
    $content = trim(F\remove($index + 1, $line)); // remove until first space included
  }
  return [$name, $content];
}
