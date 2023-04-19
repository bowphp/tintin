- [Introduction](#introduction)
- [Installation](#installation)
  - [Configuration](#configuration)
- [Usage](#usage)
  - [Configuration for Bow](#configuration-for-bow)
  - [Data display](#data-display)
    - [Display of non-escaped data](#display-of-non-escaped-data)
  - [Add a comment](#add-a-comment)
  - [%if / %elseif or %elif / %else](#if--elseif-or-elif--else)
  - [%unless](#unless)
  - [%loop / %for / %while](#loop--for--while)
    - [Using %loop](#using-loop)
    - [Syntax sugars %jump and %stop](#syntax-sugars-jump-and-stop)
    - [Using %for](#using-for)
    - [Using %while](#using-while)
  - [Include of file](#include-of-file)
  - [Condition %include of file](#condition-include-of-file)
    - [Example of inclusion](#example-of-inclusion)
    - [Example of %includeWhen or %includeIf](#example-of-includeWhen-or-includeIf)
- [Inherit with %extends, %block and %inject](#inherit-with-extends-block-and-inject)
  - [Explication](#explication)
- [Personalized directive](#personalized-directive)
  - [Example](#example)
  - [Use of directives](#use-of-directives)
  - [Compilation du template](#compilation-du-template)
  - [Output after compilation](#output-after-compilation)
  - [Add your configuration guidelines](#add-your-configuration-guidelines)

## Introduction

Tintin is a PHP template that wants to be very simple and extensible. It can be used in any PHP project.

## Installation

To install the package it will be better to use `composer` who is `php` package manager.

```bash
composer require bowphp/tintin
```

### Configuration

You can use the package simply, like this. But except that this way of doing does not allow to exploit the inheritance system in an optimal way. Use this way of doing things, only if you want to test the package or for small applications.

```php
require 'vendor/autoload.php';

$tintin = new Tintin\Tintin;

echo $tintin->render('Hello, {{ strtoupper($name) }}', [
  'name' => 'tintin'
]);
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

## Usage

```php
// Configuration made previously
$tintins = new Tintin\Tintin($loader);

$tintins->render('filename', ['name' => 'data']);
// Or
$tintins->render('dossier/filename', ['name' => 'data']);
// Or
$tintins->render('dossier.filename', ['name' => 'data']);
```

> Note that the source of the files is always the path to `path`.

### Configuration for Bow

To allow Bow to use Tintin as default template engine, he will need to make some small configuration.

Add this configuration to the file `app/Kernel.php`:

```php
public function configurations() {
  return [
    ...
    \Tintin\Bow\TintinConfiguration::class,
    ...
  ];
}
```

And again in the configuration file views located in `config/view.php`.

> Bow framework use tintin as default view engine.

```php
return [
  // Define the engine to use
  'engine' => 'tintin',

  // Extension de fichier
  'extension' => 'tintin.php'
];
```

And that's it, now your default template engine is `tintin`: +1:

### Data display

You can display the contents of the name variable as follows:

```t
Hello, {{ $name }}.
```

Of course, you are not limited to displaying the content of the variables passed to the view. You can also echo the results of any PHP function. In fact, you can insert any PHP code into an echo statement:

```t
Hello, {{ strtoupper($name) }}.
```

> Tintin instructions `{{}}` are automatically sent via the PHP function `htmlspecialchars` to prevent XSS attacks.

#### Display of non-escaped data

By default, Tintin `{{}}` instructions are automatically sent via the PHP function `htmlspecialchars` to prevent XSS attacks. If you do not want your data to be protected, you can use the following syntax:

```t
Hello, {{{ $name }}}.
```

### Add a comment

This `{## comments ##}` clause adds a comment to your `tintin` code.

### %if / %elseif or %elif / %else

These are the clauses which make it possible to establish conditional branches as in most programming languages.

```t
%if($name == 'tintin')
  {{ $name }}
%elseif($name == 'template')
  {{ $name }}
%else
  {{ $name }}
%endif
```

> You can use `%elif` instead of `%elseif`.

### %unless

Small specificity, the `%unless` meanwhile, it allows to make a reverse condition of `%if`.

To put it simply, here is an example:

```t
%unless($name == 'tintin')
# Equals to
%if(!($name == 'tintin'))
```

### %loop / %for / %while

Often you may have to make lists or repetitions on items. For example, view all users of your platform.

#### Using %loop

This clause does exactly the `foreach` action.

```t
%loop($names as $name)
  Hello {{ $name }}
%endloop
```

This clause can also be paired with any other clause such as `%if`. A quick example.

```t
%loop($names as $name)
  %if($name == 'tintin')
    Hello {{ $name }}
    %stop
  %endif
%endloop
```

You may have noticed the `%stop` it allows to stop the execution of the loop. There is also his spouse `%jump`, him parcontre allows to stop the execution at his level and launch execution of the next round of the loop.

#### Syntax sugars %jump and %stop

Often the developer is made to make stop conditions of the `%loop` like this:

```t
%loop($names as $name)
  %if($name == 'tintin')
    %stop
    // Or
    %jump
  %endif
%endloop
```

With syntactic sugars, we can reduce the code like this:

```t
%loop($names as $name)
  %stop($name == 'tintin')
  // Or
  %jump($name == 'tintin')
%endloop
```

#### Using %for

This clause does exactly the `for` action.

```t
%for($i = 0; $i < 10; $i++)
 // ..
%endfor
```

#### Using %while

This clause does exactly the `while` action.

```t
%while($name != 'tintin')
 // ..
%endwhile
```

### Include of file

Often when you are developing your code, you have to subdivide the views of your application to be more flexible and write less code.

`%include` allows to include another template file in another.

```t
%include('filename', data)
```

### Condition %include of file

Sometime you want to include a file when some condition are validate. No panic, the `%includeIf` or `%includeWhen` is here for you.

#### Example of inclusion

Consider the following `filename.tintin.php` file:

```t
Hello {{ $name }}
```

Use:

```t
%include('filename', ['name' => 'Tintin'])
// => Hello Tintin
```

#### Example of %includeWhen or %includeIf

Sometimes you would like to include content when a condition is well defined. So to do this you can use `%includeIf` or `%includeWhen`

```t
%includeWhen(!$user->isAdmin(), "filename", ["name" => "Tintin"])
```

> Tintin will execute the templae only if the `!$user->isAdmin()` condition is correct

## Inherit with %extends, %block and %inject

Like any good template system **tintin** supports code sharing between files. This makes your code flexible and maintainable.

Consider the following **tintin** code:

```t
# The `layout.tintin.php` file
<!DOCTYPE html>
<html>
<head>
  <title>Hello, world</title>
</head>
<body>
  <h1>Page header</h1>
  <div>
    %inject('content')
  </div>
  <p>Page footer</p>
</body>
</html>
```

And also, we have another file that inherits the code of the file `layout.tintin.php`

```t
// the file is named `content.tintin.php`
%extends('layout')

%block('content')
  <p>This is the page content</p>
%endblock
```

### Explication

The `content.tintin.php` file will inherit the code from` layout.tintin.php` and if you mark it well, in the file `layout.tintin.php` we have the clause `%inject` which has as parameter the name of `content.tintin.php` `block` which is `content`. Which means that the content of `%block` `content` will be replaced by `%inject`. Which will give in the end this:

```html
<!DOCTYPE html>
<html>
<head>
  <title>Hello, world</title>
</head>
<body>
  <h1>Page header</h1>
  <div>
    <p>This is the page content</p>
  </div>
  <p>Page footer</p>
</body>
</html>
```

## Personalized directive

Tintin can be expanded with its custom directive system, to do this used the method `directive`

```php
$tintin->directive('hello', function (string $name) {
  return 'Hello, '. $name;
});

echo $tintin->render('%hello("Tintin")');
// => Hello, Tintin
```

### Example

Creating a directive to manage a form:

```php
$tintin->directive('input', function (string $type, string $name, ?string $value) {
  return '<input type="'.$type.'" name="'.$name.'" value="'.$value.'" />';
});

$tintin->directive('textarea', function (string $name, ?string $value) {
  return '<textarea name="'.$name.'">"'.$value.'"</textarea>';
});

$tintin->directive('button', function (string $type, string $label) {
  return '<button type="'.$type.'">'.$label.'"</button>';
});

$tintin->directive('form', function (string $action, string $method, string $enctype = "multipart/form-data") {
  return '<form action="'.$action.'" method="'.$method.'" enctype="'.$enctype.'">';
});

$tintin->directive('endform', function () {
  return '</form>';
});
```

### Use of directives

To use these guidelines, nothing is easier. Write the name of the directive preceded by `%`. Then if this directive takes parameters, launch the directive as you run the functions in your program.

```t
// File form.tintin.php
%form("/posts", "post", "multipart/form-data")
  %input("text", "name")
  %textarea("content")
  %button('submit', 'Add')
%endform
```

### Compilation du template

The compilation is done as usual, for more information on the [compilation](#use).

```php
echo $tintin->render('form');
```

### Output after compilation

```html
<form action="/posts" method="post" enctype="multipart/form-data">
  <input type="text" name="name" value="" />
  <textarea name="content"></textarea>
  <button type="submit">Add</button>
</form>
```

### Add your configuration guidelines

In case you use the Tintin configuration for Bow Framework.
Change your configuration in the `ApplicationController::class` in the `app/Configurations` folder.

```php
namespace App\Configurations;

class ApplicationConfiguration extends Configuration
{
  /**
   * Launch configuration
   *
   * @param Loader $config
   * @return void
   */
  public function create(Loader $config): void
  {
    $tintin = app('view')->getTemplate();

    $tintin->directive('super', function () {
      return "Super !";
    });
  }
}
```

Now the `%super` directive is available and you can use it.

```php
return $tintin->render('%super');
// => Super !
```
