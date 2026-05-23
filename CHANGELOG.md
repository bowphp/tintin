# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Fixed

* Fix greedy `condition_pattern` that produced broken PHP when an `%if`, `%unless`, `%isset`, `%loop`, `%while`, or `%for` directive sat on a single line with a `{{ }}` echo in its body (the head would extend past the real `)` and swallow the echo's closing paren). Replaced `(.*)` with a recursive balanced-paren matcher in `Compiler::$condition_pattern`.

## 3.1.5 - 2025-12-21

### What's Changed

* feat: add english translate by @papac in https://github.com/bowphp/tintin/pull/83
* Fix stack building where error when it made deep compilation by @papac in https://github.com/bowphp/tintin/pull/84

**Full Changelog**: https://github.com/bowphp/tintin/compare/3.1.4...3.1.5

## 3.0.0 - 2023-05-11

Change the core language

- We use now the % symbol to identify tintin directives
- Add the new directive like `%import` or `%macro`

## Pre-3.0.0 notes

- Change the template lexique from # to %
- Remove the default configuration
- Code formatting
- Refactoring of the code base
- Add the %auth and %endauth tags
- Add the %guest and %endguest tags
- Add the %flash tags for show the flash message
- Add the %auth and %endauth tags
- Add the `getEngine` method for Bow Framework integration
