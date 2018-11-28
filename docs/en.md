<h1 align="center">
    <img src="https://github.com/bowphp/arts/raw/master/tintin.jpeg" width="150px">
    <br/>Tintin
</h1>

<p align="center">The sample php Template</p>
<p align="center">
    <a href="https://github.com/bowphp/docs/blog/master/tintin.md" title="docs"><img src="https://img.shields.io/badge/docs-read%20docs-blue.svg?style=flat-square"/></a>
    <a href="https://packagist.org/packages/bowphp/tintin" title="version"><img src="https://img.shields.io/packagist/v/bowphp/tintin.svg?style=flat-square"/></a>
    <a href="https://github.com/bowphp/tintin/blob/master/LICENSE" title="license"><img src="https://img.shields.io/github/license/mashape/apistatus.svg?style=flat-square"/></a>
    <a href="https://travis-ci.org/bowphp/tintin" title="Travis branch"><img src="https://img.shields.io/travis/bowphp/tintin/master.svg?style=flat-square"/></a>
</p>

## Installation

To install the package it will be better to use `composer` who is `php` package manager.

```bash
composer require bowphp/tintin
```

## Usage

You can use the package simply, like this. But except that this way of doing does not allow to exploit the inheritance system in an optimal way. Use this way of doing things, only if you want to test the package or for small applications.

```php
require 'vendor/autoload.php';

$tintin = new Tintin\Tintin;

echo $tintin->render('Hello, world {{ strtoupper($name) }}', ['name' => 'tintin']);
// -> Hello, world TINTIN
```

To use the package properly, you must rather follow the installation that follows:

```php
require 'vendor/autoload.php';

$loader = new Tintin\Loader\Filesystem([
  'path' => '/path/to/the/views/source',
  'extension' => 'tintin.php',
  'cache' => '/path/to/the/cache/directory'
]);

$tt = new Tintin\Tintin($loader);
```

| paramêtre | Description |
|---------|-------------|
| __php__ | The path to the views folder of your applications |
| __extension__ | the extension of the template files. By default, the value is `tintin.php` |
| __cache__ | The cache folder. This is where `tintin` will create the cache. If it is not set, `tintin` will cache the compiled files in the temporary `php` directory. |

Example:

```php
// Configuration made previously
$tt = new Tintin\Tintin($loader);

$tt->render('filename', ['name' => 'data']);
// Ou
$tt->render('dossier/filename', ['name' => 'data']);
// Ou
$tt->render('dossier.filename', ['name' => 'data']);
```

> Note that the source of the files is always the path to `path`.

### add a comment

This `{# comments #}` clause adds a comment to your `tintin` code.

### `#if` / `#elseif` or `#elif`  / `#else` 

Ce sont les clauses qui permettent d'établir des branchements conditionnels comme dans la plupart des langages de programmation.

```
#if ($name == 'tintin')
  {{ $name }}
#elseif ($name == 'template')
  {{ $name }}
#else
  {{ $name }}
#endif
```

> You can use `#elif` instead of `#elseif`.

### `#unless`

Small specificity, the `#unless` meanwhile, it allows to make a reverse condition of `#if`.
To put it simply, here is an example:

```
#unless ($name == 'tintin') => #if (!($name == 'tintin'))
```

### `#loop` as `foreach` / `#for`, `#while`

Often you may have to make lists or repetitions on items. For example, view all users of your platform.

#### Using `# loop`

This clause does exactly the `foreach` action.

```
#loop ($names as $name)
  Bonjour {{ $name }}
#endloop
```

This clause can also be paired with any other clause such as `#if`.
A quick example.

```
#loop ($names as $name)
  #if ($name == 'tintin')
    Bonjour {{ $name }}
    #stop
  #endif
#endloop
```

You may have noticed the `#stop` it allows to stop the execution of the loop. There is also his spouse `#jump` ', him parcontre allows to stop the execution at his level and launch execution of the next round of the loop.

#### Syntax sugars `#jump (condition)` and `#stop (condition)`

Often the developer is made to make stop conditions of the `#loop` like this:

```
#loop ($names as $name)
  #if ($name == 'tintin')
    #stop
    // Ou
    #jump
  #endif
#endloop
```

With syntactic sugars, we can reduce the code like this:

```
#loop ($names as $name)
  #stop($name == 'tintin')
  // Ou
  #jump($name == 'tintin')
#endloop
```

#### Using `# for`

This clause does exactly the `for` action.

```
#for ($i = 0; $i < 10; $i++)
 // ..
#endfor
```

#### Using `#while`

This clause does exactly the `while` action.

```
#while ($name != 'tintin')
 // ..
#endwhile
```

### Include file with `#include`

Often when you are developing your code, you have to subdivide the views of your application to be more flexible and write less code.

`#include` allows to include another template file in another.

```
 #include('filename', data)
```

#### Example of inclusion

Consider the following `hello.tintin.php` file:

```jinja
Hello {{ $name }}
```

Use:

```
#include('hello', ['name' => 'Tintin'])
// => Hello Tintin
```

## Inherit with `#extends`,` #block` and `#inject`

Like any good template system **tintin** supports code sharing between files. This makes your code flexible and maintainable.

Consider the following **tintin** code:

```
// The `layout.tintin.php` file
<!DOCTYPE html>
<html>
<head>
  <title>Hello, world</title>
</head>
<body>
  <h1>Page header</h1>
  <div id="page-content">
    #inject('content')
  </div>
  <p>Page footer</p>
</body>
</html>
```

And also, we have another file that inherits the code of the file `layout.tintin.php`

```
// the file is named `content.tintin.php`
#extends('layout')

#block('content')
  <p>This is the page content</p>
#endblock
```

### Explication

The `content.tintin.php` file will inherit the code from` layout.tintin.php` and if you mark it well, in the file `layout.tintin.php` we have the clause `#inject` which has as parameter the name of `content.tintin.php` `block` which is `content`. Which means that the content of `# block` `content` will be replaced by `#inject`. Which will give in the end this:

```
<!DOCTYPE html>
<html>
<head>
  <title>Hello, world</title>
</head>
<body>
  <h1>Page header</h1>
  <div id="page-content">
    <p>This is the page content</p>
  </div>
  <p>Page footer</p>
</body>
</html>
```

# Contribution

To participate in the project you must:

+ Fork the project so that it is among the directories of your github ex account: `https://github.com/your-account/app`
+ Clone the project from your github `git clone account https://github.com/your-account/tintin`
+ Create a branch whose name will be the summary of your change `git branch branch-of-your-works`
+ Make a publication on your depot `git push origin branch-of-your-works`
+ Finally make a [pull-request](https://www.thinkful.com/learn/github-pull-request-tutorial/Keep-Tabs-on-the-Project#Time-to-Submit-Your-First-PR)

Or go to the [issues](https://github.com/bowphp/tintin/issues) page, make your corrections and finally follow [publish](#contribution).

## Auteur

**Franck DAKIA** is a Full Stack developer currently based in Africa, Ivory Coast, Abidjan. Passionate about code, and collaborative development, Speaker, Trainer and Member of several communities of developers.

Contact: [dakiafranck@gmail.com](mailto:dakiafranck@gmail.com) - [@franck_dakia](https://twitter.com/franck_dakia)

**Please, if there is a bug on the project please contact me by email or leave me a message on the [slack](https://bowphp.slack.com).**