# common



- [alias](#alias)
- [codes_from_description](#codes_from_description)
- [split_code](#split_code)


# alias
```php
function alias(string $name) : string
```

Returns the full name of a function within this project.
```php
alias('parse_file'); //=> 'Wn\Mate\parse_file'
```

# codes_from_description
```php
function codes_from_description(string $description) : array<string>
```

Gets an array of code snippets from a text.
```php
$description = "some text\n```php\nfirst code\n```\nother text\n```php\nother\ncode\n```";
codes_from_description($description); //=> [
//   "first code",
//   "other\ncode"
// ]
```

# split_code
```php
function split_code(string $code) : array
```

Splits code into multiple statements.
```php
$code = "\$n = 5;\n\$add = function (\$a) {\n  return \$a + 1;\n};\n\$y = \$add(\$n);";

split_code($code); //=> [
//   '$n = 5;',
//   "\$add = function (\$a) {\n  return \$a + 1;\n};",
//   '$y = $add($n);'
// ]
```