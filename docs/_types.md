# _types



- [Block](#block)
- [Config](#config)
- [Doc](#doc)
- [File](#file)
- [FunctionBlock](#function_block)
- [FunctionDoc](#function_doc)
- [FunctionTest](#function_test)
- [Module](#module)
- [Parameter](#parameter)
- [Tag](#tag)
- [Test](#test)
- [TypeBlock](#type_block)
- [TypeDoc](#type_doc)
- [UnknownBlock](#unknown_block)

# Block
One of `FunctionBlock`, `TypeBlock` or `UnknownBlock`.


# Config

```php
{
  string $srcDir,
  string $docsDir,
  string $testsDir,
  string $cachePath
}
```

# Doc

```php
{
  string $path;,
  string $title;,
  string $header;,
  array<TypeDoc> $types;,
  array<FunctionDoc> $functions;
}
```

# File

```php
{
  string $path,
  string $content
}
```

# FunctionBlock

```php
{
  string $type,
  string $name,
  string $description,
  array<Parameter> $args,
  string $returnType,
  array<Tag> $tags
}
```

# FunctionDoc

```php
{
  string $name,
  string $description,
  string $signature,
  string $example
}
```

# FunctionTest

```php
{
  string $name,
  string $code
}
```

# Module

```php
{
  string $path,
  string $description,
  string $namespace,
  array<string> $uses,
  array<TypeBlock> $types,
  array<FunctionBlock> $functions
}
```

# Parameter

```php
{
  string $name,
  string $type,
  string $description
}
```

# Tag

```php
{
  string $name,
  string $content
}
```

# Test

```php
{
  string $path;,
  string $namespace;,
  array<string> $uses;,
  string $name;,
  string $parent;,
  array<FunctionTest> $functions;,
  string $globals;
}
```

# TypeBlock

```php
{
  string $type,
  string $name,
  string $description,
  array<Parameter> $fields,
  array<Tag> $tags
}
```

# TypeDoc

```php
{
  string $name,
  string $description,
  array<Parameter> $fields
}
```

# UnknownBlock

```php
{
  string $type,
  string $name,
  string $description,
  array<Parameter> $fields,
  array<Tag> $tags
}
```
