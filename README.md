# Mate

[![Build Status](https://travis-ci.org/webNeat/mate.svg?branch=master)](https://travis-ci.org/webNeat/mate)
[![Coverage Status](https://coveralls.io/repos/github/webNeat/mate/badge.svg?branch=master)](https://coveralls.io/github/webNeat/mate?branch=master)
[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.me/webneat)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](https://github.com/webNeat/mate/blob/master/LICENSE)

# Contents

- [What is Mate?](#what-is-mate?)
- [Requirements](#requirements)
- [Installation](#installation)
- [Screenshots](#screenshots)
- [Command line options](#command-line-options)
- [Config File](#config-file)
- [Development Notes](#development-notes)

# What is Mate?

**Mate** is a tool to generate documentation and tests from PHPDoc comments for functional libraries.

> What is a PHPDoc comment?

it's a comment like

```php
/**
 * Description here.
 * @tag value
 * @other-tag some other value
 */
```

> Ok, and what do you mean by "functional library"?

I mean a group of [pure functoions](https://en.wikipedia.org/wiki/Pure_function) and type definitions. Testing pure functions is easy and can be done inside a comment. Testing a function which has side effects or a class that alters its internal state would be complicated and is not part of Mate features (yet?).

> Hmm, so you mean that I can't use Mate if my project contains classes or non-pure functions?

You can use Mate on any project to generate documentation and tests for your pure functions. This will not influence other parts of your project. Mate can also be used to watch changes on your source files and run `phpunit` whenever a file changes.

# Requirements

- PHP 7.1+

# Installation

Install it as a dev dependency

```
composer require wn/mate --dev
```

# Screenshots

![Generating function tests](https://raw.githubusercontent.com/webneat/mate/master/docs/screenshots/tests.gif)

![Generating function documentation](https://raw.githubusercontent.com/webneat/mate/master/docs/screenshots/docs.gif)

![Generating type documentation](https://raw.githubusercontent.com/webneat/mate/master/docs/screenshots/types.gif)

# Command line options

Running `mate --help` shows the command line options

```
Mate version 1.0.0-alpha

a tool to generate documentation and tests from PHPDoc comments.

Syntax: [options] configPath
Arguments:
    configPath string Path to the config file. (default: "mate.json")
Options:
    --dont-run-tests Don't run phpunit after the build.
    --watch Watch source files for changes.
    --no-cache Don't use cache. Should not be combined with --watch.
    --no-tests Don't generate test files.
    --no-docs Don't generate documentation files.
```

- **--dont-run-tests**: by default, Mate will run `phpunit` after each build or file change.
- **--watch**: with this option, Mate will watch the source files changes and generate the changed tests and docs. It will also run `phpunit` after each change.
- **--no-cache**: by default, Mate will create a file `mate.lock` and use it as cache. This helps when watching files for changes to not regenerate all tests and docs. Use this option if you don't want Mate to use the cache for same reason.

# Config File

Here is the default config file

```json
{
  "srcDir": "src",
  "testsDir": "tests",
  "docsDir": "docs",
  "testCaseClass": "\\Wn\\Mate\\Classes\\TestCase",
  "cachePath": "mate.lock"
}
```

