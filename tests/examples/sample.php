<?php
/**
 * This is a source file to test the Mate command.
 * it contains some random functions and types definitions.
 * @mate
 * @namespace My\Namespace
 * @use Tarsana\Functional as F
 */

/**
 * Adds two numbers.
 * ```php
 * add(5, 2); //=> 7
 * add(0, 1); //=> 1
 * add('Hey', 'you'); // throws "'Hey' is not a number!"
 * ```
 * @function add
 * @param int|float $x
 * @param int|float $y
 * @return int|float
 */
function add() {}

/**
 * Represents a developer.
 *
 * @type Developer
 * @field string $name
 * @field string $email
 * @field array<Project> $projects;
 */
class Person {}
