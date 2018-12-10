- [Introduction](#introduction)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
  - [Configuration for Bow](#configuration-for-bow)
  - [Add a comment](#add-a-comment)
  - [#if / #elseif or #elif / #else](#if--elseif-or-elif--else)
  - [#unless](#unless)
  - [#loop / #for / #while](#loop--for--while)
    - [Using #loop](#using-loop)
    - [Syntax sugars #jump and #stop](#syntax-sugars-jump-and-stop)
    - [Using #for](#using-for)
    - [Using #while](#using-while)
  - [Include of file](#include-of-file)
    - [Example of inclusion](#example-of-inclusion)
- [Inherit with #extends, #block and #inject](#inherit-with-extends-block-and-inject)
  - [Explication](#explication)
- [Personalized directive](#personalized-directive)
  - [Example](#example)
  - [Use of directives](#use-of-directives)
  - [Compilation du template](#compilation-du-template)
  - [Output after compilation](#output-after-compilation)
  - [Add your configuration guidelines](#add-your-configuration-guidelines)
- [Contribution](#contribution)
- [Author](#author)

## Introduction

Tintin est un template PHP qui se veut très simple et extensible. Il peut être utilisable dans n'importe quel projet PHP.

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

## Usage

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

### Configuration for Bow

To allow Bow to use Tintin as default template engine, he will need to make some small configuration.

Add this configuration to the file `app/Kernel/Loader.php`:

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

```php
return [
  // Define the engine to use
  'engine' => 'tintin',

  // Extension de fichier
  'extension' => '.tintin.php'
];
```

And that's it, now your default template engine is `tintin`: +1:

### Data display

Vous pouvez afficher le contenu de la variable name de la manière suivante:

```c
Hello, {{ $name }}.
```

Bien entendu, vous n'êtes pas limité à afficher le contenu des variables transmises à la vue. Vous pouvez également faire écho aux résultats de toute fonction PHP. En fait, vous pouvez insérer n'importe quel code PHP dans une instruction echo Blade:

```html
Hello, {{ strtoupper($name) }}.
```

> Les instructions Tintin `{{}}` sont automatiquement envoyées via la fonction PHP `htmlspecialchars` pour empêcher les attaques XSS.

#### Affichage des données non échappées

Par défaut, les instructions Tintin `{{}}` sont automatiquement envoyées via la fonction PHP `htmlspecialchars` pour empêcher les attaques XSS. Si vous ne souhaitez pas que vos données soient protégées, vous pouvez utiliser la syntaxe suivante:

```html
Hello, {{{ $name }}}.
```

### Add a comment

This `{# comments #}` clause adds a comment to your `tintin` code.

### #if / #elseif or #elif / #else

Ce sont les clauses qui permettent d'établir des branchements conditionnels comme dans la plupart des langages de programmation.

```c
#if ($name == 'tintin')
  {{ $name }}
#elseif ($name == 'template')
  {{ $name }}
#else
  {{ $name }}
#endif
```

> You can use `#elif` instead of `#elseif`.

### #unless

Small specificity, the `#unless` meanwhile, it allows to make a reverse condition of `#if`.
To put it simply, here is an example:

```c
#unless ($name == 'tintin') => #if (!($name == 'tintin'))
```

### #loop / #for / #while

Often you may have to make lists or repetitions on items. For example, view all users of your platform.

#### Using #loop

This clause does exactly the `foreach` action.

```c
#loop ($names as $name)
  Bonjour {{ $name }}
#endloop
```

This clause can also be paired with any other clause such as `#if`.
A quick example.

```c
#loop ($names as $name)
  #if ($name == 'tintin')
    Bonjour {{ $name }}
    #stop
  #endif
#endloop
```

You may have noticed the `#stop` it allows to stop the execution of the loop. There is also his spouse `#jump`, him parcontre allows to stop the execution at his level and launch execution of the next round of the loop.

#### Syntax sugars #jump and #stop

Often the developer is made to make stop conditions of the `#loop` like this:

```c
#loop ($names as $name)
  #if ($name == 'tintin')
    #stop
    // Ou
    #jump
  #endif
#endloop
```

With syntactic sugars, we can reduce the code like this:

```c
#loop ($names as $name)
  #stop($name == 'tintin')
  // Ou
  #jump($name == 'tintin')
#endloop
```

#### Using #for

This clause does exactly the `for` action.

```c
#for ($i = 0; $i < 10; $i++)
 // ..
#endfor
```

#### Using #while

This clause does exactly the `while` action.

```c
#while ($name != 'tintin')
 // ..
#endwhile
```

### Include of file

Often when you are developing your code, you have to subdivide the views of your application to be more flexible and write less code.

`#include` allows to include another template file in another.

```c
 #include('filename', data)
```

#### Example of inclusion

Consider the following `hello.tintin.php` file:

```jinja
Hello {{ $name }}
```

Use:

```c
#include('hello', ['name' => 'Tintin'])
// => Hello Tintin
```

## Inherit with #extends, #block and #inject

Like any good template system **tintin** supports code sharing between files. This makes your code flexible and maintainable.

Consider the following **tintin** code:

```c
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

```c
// the file is named `content.tintin.php`
#extends('layout')

#block('content')
  <p>This is the page content</p>
#endblock
```

### Explication

The `content.tintin.php` file will inherit the code from` layout.tintin.php` and if you mark it well, in the file `layout.tintin.php` we have the clause `#inject` which has as parameter the name of `content.tintin.php` `block` which is `content`. Which means that the content of `# block` `content` will be replaced by `#inject`. Which will give in the end this:

```c
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

## Personalized directive

Tintin can be expanded with its custom directive system, to do this used the method `directive`

```php
$tintin->directive('hello', function (array $attributes = []) {
  return 'Hello, '. $attributes[0];
});

echo $tintin->render('#hello("Tintin")');
// => Hello, Tintin
```

### Example

Creating a directive to manage a form:

```php
$tintin->directive('input', function (array $attributes = []) {
  $attribute = $attributes[0];

  return '<input type="'.$attribute['type'].'" name="'.$attribute['name'].'" value="'.$attribute['value'].'" />';
});

$tintin->directive('textarea', function (array $attributes = []) {
  $attribute = $attributes[0];

  return '<textarea name="'.$attribute['name'].'">"'.$attribute['value'].'"</textarea>';
});

$tintin->directive('button', function (array $attributes = []) {
  $attribute = $attributes[0];

  return '<button type="'.$attribute['type'].'">'.$attribute['label'].'"</button>';
});

$tintin->directive('form', function (array $attributes = []) {
  $attribute = " ";
  
  if (isset($attributes[0])) {
    foreach ($attributes[0] as $key => $value) {
      $attribute .= $key . '="'.$value.'" ';
    }
  }

  return '<form "'.trim($attribute).'">';
});

$tintin->directive('endform', function (array $attributes = []) {
  return '</form>';
});
```

### Use of directives

To use these guidelines, nothing is easier. Write the name of the directive preceded by `#`. Then if this directive takes parameters, launch the directive as you run the functions in your program. The parameters will be grouped in the `$attributes` varibles in the added order.

```c
// File form.tintin.php
#form(['method' => 'post', "action" => "/posts", "enctype" => "multipart/form-data"])
  #input(["type" => "text", "value" => null, "name" => "name"])
  #textarea(["value" => null, "name" => "content"])
  #button(['type' => 'submit', 'label' => 'Add'])
#endform
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

You can create a class in the `app` folder, for example, with the name `CustomTintinConfiguration` which will extend Tintin's default configuration that is `\Tintin\Bow\TintinConfiguration::class` and then modify the `customizer` method.

```php
use Tintin\Tintin;

class CustomTintinConfiguration extends \Tintin\Bow\TintinConfiguration
{
  /**
   * Add action in tintin
   *
   * @param Tintin $tintin
   */
  public function customizer(Tintin $tintin)
  {
    $tintin->directive('super', function (array $attributes = []) {
      return "Super !";
    });
  }
}
```

Now the `#super` directive is available and you can use it.

```php
  return $tintin->render('#super');
  // => Super !
```

## Contribution

To participate in the project you must:

- Fork the project so that it is among the directories of your github ex account: `https://github.com/your-account/app`
- Clone the project from your github `git clone account https://github.com/your-account/tintin`
- Create a branch whose name will be the summary of your change `git branch branch-of-your-works`
- Make a publication on your depot `git push origin branch-of-your-works`
- Finally make a [pull-request](https://www.thinkful.com/learn/github-pull-request-tutorial/Keep-Tabs-on-the-Project#Time-to-Submit-Your-First-PR)

Or go to the [issues](https://github.com/bowphp/tintin/issues) page, make your corrections and finally follow [publish](#contribution).

## Author

**Franck DAKIA** is a Full Stack developer currently based in Africa, Ivory Coast, Abidjan. Passionate about code, and collaborative development, Speaker, Trainer and Member of several communities of developers.

Contact: [dakiafranck@gmail.com](mailto:dakiafranck@gmail.com) - [@franck_dakia](https://twitter.com/franck_dakia)

**Please, if there is a bug on the project please contact me by email or leave me a message on the [slack](https://bowphp.slack.com).**