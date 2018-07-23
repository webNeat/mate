# Mate

[![Build Status](https://travis-ci.org/webNeat/mate.svg?branch=master)](https://travis-ci.org/webNeat/mate)
[![Coverage Status](https://coveralls.io/repos/github/webNeat/mate/badge.svg?branch=master)](https://coveralls.io/github/webNeat/mate?branch=master)
[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.me/webneat)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](https://github.com/webNeat/mate/blob/master/LICENSE)

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

Install it globally

```
composer global require wn/mate
```

and/or as a dev dependency

```
composer require wn/mate --dev
```

# Getting Started

...
