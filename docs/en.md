- [Introduction](#introduction)
  - [Installation](#installation)
  - [Configuration](#configuration)
- [Usage](#usage)
  - [Configuration for Bow](#configuration-for-bow)
  - [Displaying data](#displaying-data)
    - [Displaying unescaped data](#displaying-unescaped-data)
    - [Add a comment](#add-a-comment)
  - [The %verbatim directive](#the-verbatim-directive)
  - [The `%if` directives](#the-if-directives)
  - [The `%loop` / `%for` / `%while` directives](#the-loop--for--while-directives)
    - [Using `%loop`](#using-loop)
    - [Syntactic sugars `%jump` and `%stop`](#syntactic-sugars-jump-and-stop)
    - [The use of `%for` and `%while`](#the-use-of-for-and-while)
  - [Conditional Classes and Styles](#conditional-classes-and-styles)
  - [Include subviews](#include-subviews)
  - [Raw PHP](#raw-php)
  - [Flash session](#flash-session)
  - [Service injection](#service-injection)
  - [Authentication Directives](#authentication-directives)
  - [Environment Guidelines](#environment-guidelines)
  - [Field CSRF](#field-csrf)
  - [Method Field](#method-field)
  - [Inheritance with %extends, %block and %inject](#inheritance-with-extends-block-and-inject)
    - [Explanation](#explanation)
    - [Custom directive](#custom-directive)
    - [Add your configuration directives](#add-your-configuration-directives)
  - [The `%macro` directive](#the-macro-directive)

# Introduction

Tintin is a PHP template that is meant to be very simple and extensible. It can be used in any PHP project.

## Installation

To install the package it will be better to use `composer` which is a `php` package manager.

```bash
composer require bowphp/tintin
```

## Configuration

You can use the package simply, like this. But except that this way of doing things does not allow you to exploit the inheritance system optimally. Use this way of doing things, only if you want to test the package or for small applications.

```php
require 'vendor/autoload.php';

$tintin = new Tintin\Tintin;

echo $tintin->render('Hello, world {{ strtoupper($name) }}', ['name' => 'tintin']);
// -> Hello, world TINTIN
```

To use the package properly, you should follow the following configuration:

```php
require 'vendor/autoload.php';

$loader = new Tintin\Loader\Filesystem([
  'path' => '/path/to/the/views/source',
  'extension' => 'tintin.php',
  'cache' => '/path/to/the/cache/directory'
]);

$tintin = new Tintin\Tintin($loader);
```

| parameter | Description |
|---------|-------------|
| __php__ | The path to the views folder of your application |
| __extension__ | the extension of the template files. By default, the value is `tintin.php` |
| __cache__ | The cache folder. This is where `tintin` will create the cache. If it is not set, `tintin` will cache the compiled files in the temporary directory of `php`. |

# Usage

```php
// Configuration done beforehand
$tintin = new Tintin\Tintin($loader);

$tintin->render('filename', ['name' => 'data']);
// Or
$tintin->render('folder/filename', ['name' => 'data']);
// Or
$tintin->render('folder.filename', ['name' => 'data']);
```

> Note that the source of the files is always the path to `path`.

## Configuration for Bow

To allow Bow to use Tintin as the default template engine, you will need to do some small configuration.

Add this configuration in the `app/Kernel.php` file:

```php
public function configurations() {
  return [
    ...
    \Tintin\Bow\TintinConfiguration::class,
    ...
  ];
}
```

And again in the views configuration file located in `config/view.php`.

> Bow framework currently uses tintin as the default template

```php
return [
// Set the engine to use
  'engine' => 'tintin',

  // File extension
  'extension' => '.tintin.php'
];
```

And that's it, now your default template engine is `tintin` :+1:

## Displaying data

You can display the content of the name variable in the following way:

```t
Hello, {{ $name }}.
```

Of course, you are not limited to displaying the contents of variables passed to the view. You can also echo the results of any PHP function. In fact, you can insert any PHP code into an echo Tintin statement:

```t
Hello, {{ strtoupper($name) }}.
```

> Tintin `{{ }}` statements are automatically sent through the PHP `htmlspecialchars` function to prevent XSS attacks.

### Displaying unescaped data

By default, Tintin `{{ }}` statements are automatically sent through the PHP `htmlspecialchars` function to prevent XSS attacks. If you do not want your data to be protected, you can use the following syntax:

```t
Hello, {{{ $name }}}.
```

### Add a comment

This clause `{## ##}` allows you to add a comment to your `tintin` code.

```t
{## This comment will not be present in the rendered HTML ##}
```

## The %verbatim directive

If you display JavaScript variables in a large part of your template, you can wrap the HTML code in the `%verbatim` directive so that you don't have to prefix each Tintin echo statement with a `%` symbol:

```t
%verbatim
<div class="container">
  Hello, {{ name }}.
</div>
%endverbatim
```

## The `%if` directives

These are the clauses that allow you to establish conditional branching as in most programming languages.

You can build if statements using the `%if`, `%elseif`, `%elif`, `%else`, and `%endif` directives. These directives work the same way as their PHP counterparts:

```t
%if ($name == 'tintin')
  {{ $name }}
%elseif ($name == 'template')
  {{ $name }}
%else
  {{ $name }}
%endif
```

> You can use `%elif` instead of `%elseif`.

A small specificity, the `%unless` allows to make a condition inverse to the `%if`.
To make it simple, here is an example:

```t
%unless ($user->isAdmin())
  // do something else
$endunless
```

In addition to the conditional directives already discussed, the `%isset`, `%empty`, `%notempty` directives can be used as convenient shortcuts for their respective PHP functions:

```t
%isset($records)
  // $records is defined and is not null...
%endisset

%empty($records)
  // $records is "empty"...
%endempty

%notempty($records)
  // $records is not "empty"...
%notendempty
```

> You can add `%esle` to perform an opposite action

## The `%loop` / `%for` / `%while` directives

Often you may need to make lists or repetitions on elements. For example, display all users of your platform.

### Using `%loop`

This clause does exactly the action of `foreach`.

```t
%loop($names as $name)
  Hello {{ $name }}
%endloop
```

This clause can also be coupled with any other clause such as `%if`.
A quick example.

```t
%loop($names as $name)
  %if($name == 'tintin')
    Hello {{ $name }}
    %stop
  %endif
%endloop
```

You may have noticed the `%stop` it allows to stop the execution of the loop. There is also its spouse the `%jump`, it on the other hand allows to stop the execution at its level and to launch the execution of the next turn of the loop.

### Syntactic sugars `%jump` and `%stop`

Often the developer is led to make conditions to stop the loop `%loop` like this:

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

### The use of `%for` and `%while`

This clause does exactly the action of `for`.

```t
%for($i = 0; $i < 10; $i++)
 //..
%endfor
```

This clause does exactly the action of `while`.

```t
%while($name != 'tintin')
 //..
%endwhile
```

## Conditional Classes and Styles

The `%class` directive conditionally compiles a CSS class string. The directive accepts an array of classes where the array key contains the class(es) you want to add, while the value is a boolean expression. If the array element has a numeric key, it will always be included in the rendered class list:

```t
%php
  $isActive = false;
  $hasError = true;
%endphp

<span %class([
  'p-4',
  'font-bold' => $isActive,
  'text-gray-500' => ! $isActive,
  'bg-red' => $hasError,
])></span>

<span class="p-4 text-gray-500 bg-red"></span>
```

Similarly, the `%style` directive can be used to conditionally add inline CSS styles to an HTML element:

```t
%php
  $isActive = true;
%endphp

<span %style([
'background-color: red',
'font-weight: bold' => $isActive,
])></span>

<span style="background-color: red; font-weight: bold;"></span>
```

## Include subviews

Often when you develop your code, you are led to subdivide the views of your application to be more flexible and write less code.

`%include` allows you to include another template file in another.

```t
<div id="container">
  %include('filename', %data)
</div>
```

If you try to include a view that does not exist, Tintin will throw an error. If you want to include a view that may or may not be present, you should use the `%includeIf` directive:

```t
%includeIf("filename", ["name" => "Tintin"])
```

If you want to `%include` a view if a given boolean expression evaluates to true or false, you can use the `%includeWhen` and `%includeUnless` directives:

```t
%includeWhen($user->isAdmin(), "include-file-name", ["name" => "Tintin"])
```

## Raw PHP

In some situations, it is useful to embed PHP code in your views. You can use the Tintin `%php` or `%raw` directive to execute a simple block of PHP in your template:

```t
%php
  $counter = 1;
%endphp

%raw
  $counter = 1;
%endraw
```

## Flash session

In case you want to display a flash message directly on view you can use `%flash`. And to check if a flash message exists `%hasflash` and `%endhasflash` :

```t
%hasflash("error")
  <div class="alert alert-danger">
    %flash("error")
  </div>
%endhasflash
```

## Service injection

The `%service` directive can be used to retrieve a service from the container. The first argument passed to `%service` is the name of the variable in which the service will be placed, while the second argument is the name of the class or interface of the service you want to resolve:

```t
%service('user_service', 'App\Services\UserService')

<div>
  %loop($user_service->all() as $user)
    <p>{{ $user->name }}</p>
  %endloop
</div>
```

## Authentication Directives

The `%auth` and `%guest` directives can be used to quickly determine whether the current user is authenticated or a guest:

```t
%auth
  // The user is authenticated...
%endauth

%guest
  // The user is not authenticated...
%endguest
```

If needed, you can specify the authentication guard that should be checked when using the directives `%auth` and `%guest`:

```t
%auth('admin')
  // The user is authenticated...
%endauth

%guest('admin')
  // The user is not authenticated...
%endguest
```

## Environment Guidelines

You can check if the application is running in the production environment using the `%production` directive:

```t
%production
  // Production specific content...
%endproduction
```

Or, you can determine if the application is running in a specific environment using the `%env` directive:

```t
%env('staging')
  // The application is running in "staging"...
%endenv

%env(['staging', 'production'])
  // The application is running in "staging" or "production"...
%endenv
```

## Field CSRF

Whenever you define an HTML form in your application, you must include a hidden CSRF token field in the form so that the CSRF protection middleware can validate the request. You can use the Tintin `%csrf` directive to generate the token field:

```t
<form method="POST" action="/profile">
  %csrf
</form>
```

## Method Field

Since HTML forms cannot perform `PUT`, `PATCH` or `DELETE` requests, you will need to add a hidden _method field to spoof these HTTP verbs. Tintin's `%method` directive can create this field for you:

```t
<form action="/foo/bar" method="POST">
  %method('PUT')
</form>
```

## Inheritance with %extends, %block and %inject

Like any good template system __tintin__ supports sharing code between files. This makes your code flexible and maintainable.

Consider the following __tintin__ code:

```html+t
# the `layout.tintin.php` file
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

And also, we have another file that inherits the code from the `layout.tintin.php` file

```t
# the file is called `content.tintin.php`
%extends('layout')

%block('content')
<p>This is the page content</p>
%endblock
```

### Explanation

The `content.tintin.php` file will inherit the code from `layout.tintin.php` and if you notice, in the file `layout.tintin.php` we have the clause `%inject` which has as parameter the name of the `%block` of `content.tintin.php` which is `content`. Which means that the content of the `%block` `content` will be replaced by `%inject`. Which will give at the end this:

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

### Custom directive

Tintin can be extended with its custom directive system, to do this use the `directive` method
Let's create directives to manage a form:

```php
$tintin->directive('input', function (string $type, string $name, ?string $value) {
  return '<input type="'.$type.'" name="'.$name.'" value="'.$value.'" />';
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

To use these directives, nothing could be simpler. Write the name of the directive preceded by `%`. Then if this directive takes parameters, launch the directive as you launch the functions in your program.

```t
<div class="container">
  %form("/posts", "post", "multipart/form-data")
    %input("text", "name")
    %button('submit', 'Add')
  %endform
</div>
```

Compilation is done as usual, for more information on [compilation](#usage).

```php
echo $tintin->render('form');
```

Output after compilation:

```html
<form action="/posts" method="post" enctype="multipart/form-data">
<input type="text" name="name" value="" />
<button type="submit">Add</button>
</form>
```

### Add your configuration directives

In case you are using the Tintin configuration for Bow Framework.
Change your configuration in the `ApplicationController::class` in the `app/Configurations` folder.

```php
namespace App\Configurations;

use Bow\Configuration\Loader as Config;

class ApplicationConfiguration extends Configuration
{
  /**
  * Launch configuration
  *
  * @param Config $config
  * @return void
  */
  public function create(Config $config): void
  {
    $tintin = app('view')->getEngine();

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

## The `%macro` directive

Often, you will be led to use or reuse a template block to optimize the writing of your application. So macros are there for that.

> Macros must be defined in a separate file.

To use the `%macro` you must pass first the macro name and then the macro parameters.

Consider the file `user-macro.tintin.php`.

```t
%macro('users', array $users)
%loop($users as $user)
<div>{{ $user }}</div>
%endloop
%endmacro
```

To use the macro you must import it into another file with `%import`. We will call the file `app.tintin.php` containing the following template:

```t
%import('user-macro')

%extends('layout')

%block('content')
<div class="container">
{{ users($users) }}
</div>
%endblock
```

For the compilation we will pass the following list of users:

```php
$users = ["franck", "lucien", "brice"];

$tintin->render('app', compact('users'));
```

After compiling the file

```html
<div>franck</div>
<div>lucien</div>
<div>brice</div>
```
