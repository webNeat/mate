<?php
namespace Wn\Mate;

/**
 * @mate
 * @type Block
 * One of `FunctionBlock`, `TypeBlock` or `UnknownBlock`.
 */
class Block {
  public $type = 'unknown';
  public $description;
  public $tags;
}
