# sample

/**
This is a source file to test the Mate command.
it contains some random functions and types definitions.
*/

- [add](#add)


# add
```php
function add(int|float $x, int|float $y) : int|float
```
/**
Adds two numbers.
*/
```php
add(5, 2); //=> 7
add(0, 1); //=> 1
add('Hey', 'you'); // throws "'Hey' is not a number!"
```