- [Introduction](#introduction)
- [Installation](#installation)
  - [Configuration](#configuration)
- [Utilisation](#utilisation)
  - [Configuration pour Bow](#configuration-pour-bow)
  - [Affichage des données](#affichage-des-données)
    - [Affichage des données non échappées](#affichage-des-données-non-échappées)
  - [Ajouter un commentaire](#ajouter-un-commentaire)
  - [La directive %verbatim](#la-directive-verbatim)
  - [les directives `%if`](#les-directives-if)
  - [Les directives `%loop` / `%for` / `%while`](#les-directives-loop--for--while)
    - [L'utilisation de `%loop`](#lutilisation-de-loop)
    - [Les sucres syntaxiques `%jump` et `%stop`](#les-sucres-syntaxiques-jump-et-stop)
    - [L'utilisation de `%for`](#lutilisation-de-for)
    - [La directive `%while`](#la-directive-while)
  - [Inclusion de fichier](#inclusion-de-fichier)
    - [Exemple d'inclusion](#exemple-dinclusion)
    - [Exemple de `%includeWhen` ou `%includeIf`](#exemple-de-includewhen-ou-includeif)
  - [Directives d'authentification](#directives-dauthentification)
  - [Environment Guidelines](#environment-guidelines)
- [Héritage avec %extends, %block et %inject](#héritage-avec-extends-block-et-inject)
  - [Explication](#explication)
- [Directive personnelisée](#directive-personnelisée)
  - [Exemple](#exemple)
  - [Utilisation des directives](#utilisation-des-directives)
  - [Compilation du template](#compilation-du-template)
  - [Sortie après compilation](#sortie-après-compilation)
  - [Ajouter vos directives de la configuration](#ajouter-vos-directives-de-la-configuration)
  - [La directive `%macro`](#la-directive-macro)

## Introduction

Tintin est un template PHP qui se veut très simple et extensible. Il peut être utilisable dans n'importe quel projet PHP.

## Installation

Pour installer le package il sera plus mieux utiliser `composer` qui est gestionnaire de package `php`.

```bash
composer require bowphp/tintin
```

### Configuration

Vous pouvez utiliser le package simplement, comme ceci. Mais sauf que cette façon de faire ne permet pas d'exploiter le système d'héritage de façon optimal. Utilisez cette façon de faire, seulement si vous voulez tester le package ou pour les petites applications.

```php
require 'vendor/autoload.php';

$tintin = new Tintin\Tintin;

echo $tintin->render('Hello, world {{ strtoupper($name) }}', ['name' => 'tintin']);
// -> Hello, world TINTIN
```

Pour utiliser proprement le package, il faut suivre plutôt la configuration qui suivant:

```php
require 'vendor/autoload.php';

$loader = new Tintin\Loader\Filesystem([
  'path' => '/path/to/the/views/source',
  'extension' => 'tintin.php',
  'cache' => '/path/to/the/cache/directory'
]);

$tintin = new Tintin\Tintin($loader);
```

| paramêtre | Description |
|---------|-------------|
| __php__ | Le chemin vers le dossier des vues de votre applications |
| __extension__ | l'extension des fichiers de template. Par defaut, la valeur est `tintin.php` |
| __cache__ | Le dossier de cache. C'est là que `tintin` va créé le cache. S'il n'est pas défini, `tintin` mettra en cache les fichiers compilés dans le répertoire temporaire de `php`.  |

## Utilisation

```php
// Configuration faite qu préalabe
$tintin = new Tintin\Tintin($loader);

$tintin->render('filename', ['name' => 'data']);
// Ou
$tintin->render('dossier/filename', ['name' => 'data']);
// Ou
$tintin->render('dossier.filename', ['name' => 'data']);
```

> Notez que la source des fichiers est toujour le chemin vers `path`.

### Configuration pour Bow

Pour permet à Bow d'utiliser Tintin comme moteur de template par defaut, il va faloir faire quelque petit configuration.

Ajouter cette configuration dans le fichier `app/Kernel.php`:

```php
public function configurations() {
  return [
    ...
    \Tintin\Bow\TintinConfiguration::class,
    ...
  ];
}
```

Et encore dans le fichier de configuration des vues situés dans `config/view.php`.

> Bow framework utilise actuellement tintin comme template par défaut

```php
return [
  // Définir le moteur à utiliser
  'engine' => 'tintin',

  // Extension de fichier
  'extension' => '.tintin.php'
];
```

Et c'est tout, désormais votre moteur de template par defaut est `tintin` :+1:

### Affichage des données

Vous pouvez afficher le contenu de la variable name de la manière suivante:

```t
Hello, {{ $name }}.
```

Bien entendu, vous n'êtes pas limité à afficher le contenu des variables transmises à la vue. Vous pouvez également faire écho aux résultats de toute fonction PHP. En fait, vous pouvez insérer n'importe quel code PHP dans une instruction echo Tintin:

```t
Hello, {{ strtoupper($name) }}.
```

> Les instructions Tintin `{{ }}` sont automatiquement envoyées via la fonction PHP `htmlspecialchars` pour empêcher les attaques XSS.

#### Affichage des données non échappées

Par défaut, les instructions Tintin `{{ }}` sont automatiquement envoyées via la fonction PHP `htmlspecialchars` pour empêcher les attaques XSS. Si vous ne souhaitez pas que vos données soient protégées, vous pouvez utiliser la syntaxe suivante:

```t
Hello, {{{ $name }}}.
```

### Ajouter un commentaire

Cette clause `{## comments ##}` permet d'ajouter un commentaire à votre code `tintin`.

### La directive %verbatim

Si vous affichez des variables JavaScript dans une grande partie de votre modèle, vous pouvez envelopper le code HTML dans la directive `%verbatim` afin de ne pas avoir à préfixer chaque instruction Blade echo avec un symbole `%` :

```t
%verbatim
  <div class="container">
    Hello, {{ name }}.
  </div>
%endverbatim
```

### les directives `%if`

Ce sont les clauses qui permettent d'établir des branchements conditionnels comme dans la plupart des langages de programmation.

Vous pouvez construire des instructions if en utilisant les directives `%if`, `%elseif`, `%elif`, `%else` et `%endif`. Ces directives fonctionnent de la même manière que leurs homologues PHP :

```t
%if ($name == 'tintin')
  {{ $name }}
%elseif ($name == 'template')
  {{ $name }}
%else
  {{ $name }}
%endif
```

> Vous pouvez utiliser `%elif` à la place de `%elseif`.

Petite spécificité, le `%unless` quant à lui, il permet de faire une condition inverse du `%if`.
Pour faire simple, voici un exemple:

```t
%unless($user->isAdmin())
  // do something else
$endunless
```

En plus des directives conditionnelles déjà discutées, les directives `%isset` et `%empty` peuvent être utilisées comme raccourcis pratiques pour leurs fonctions PHP respectives :

```t
%isset($records)
  // $records is defined and is not null...
%endisset
 
%empty($records)
  // $records is "empty"...
%endempty
```

### Les directives `%loop` / `%for` / `%while`

Souvent vous pouvez être amener à faire des listes ou répétitions sur des éléments. Par exemple, afficher tout les utilisateurs de votre plateforme.

#### L'utilisation de `%loop`

Cette clause faire exactement l'action de `foreach`.

```t
%loop($names as $name)
  Bonjour {{ $name }}
%endloop
```

Cette clause peux être aussi coupler avec tout autre clause telque `#if`.
Un exemple rapide.

```t
%loop($names as $name)
  %if($name == 'tintin')
    Bonjour {{ $name }}
    %stop
  %endif
%endloop
```

Vous avez peut-être remarquer le `%stop` il permet de stoper l'éxécution de la boucle. Il y a aussi son conjoint le `%jump`, lui parcontre permet d'arrêter l'éxécution à son niveau et de lancer s'éxécution du prochain tour de la boucle.

#### Les sucres syntaxiques `%jump` et `%stop`

Souvent le dévéloppeur est amené à faire des conditions d'arrêt de la boucle `%loop` comme ceci:

```t
%loop($names as $name)
  %if($name == 'tintin')
    %stop
    // Ou
    %jump
  %endif
%endloop
```

Avec les sucres syntaxique, on peut réduire le code comme ceci:

```t
%loop($names as $name)
  %stop($name == 'tintin')
  // Ou
  %jump($name == 'tintin')
%endloop
```

#### L'utilisation de `%for`

Cette clause faire exactement l'action de `for`.

```t
%for($i = 0; $i < 10; $i++)
 // ..
%endfor
```

#### La directive `%while`

Cette clause faire exactement l'action de `while`.

```t
%while($name != 'tintin')
 // ..
%endwhile
```

### Inclusion de fichier

Souvent lorsque vous dévéloppez votre code, vous êtes amener à subdiviser les vues de votre application pour être plus flexible et écrire moin de code.

`%include` permet d'include un autre fichier de template dans un autre.

```t
 %include('filename', data)
```

#### Exemple d'inclusion

Considérons le fichier `filename.tintin.php` contenant le code suivant:

```t
Hello, {{ $name }}
```

Utilisation:

```t
%include('filename', ['name' => 'Tintin'])
// => Hello Tintin
```

#### Exemple de `%includeWhen` ou `%includeIf`

Parfois vous aimeriez inclut un contenu quand une condition est bien définit, alors pour se faire vous pouvez utiliser `%includeWhen` et dans certain si la vue à intégrer exists alors `%includeIf`

```t
%includeWhen(!$user->isAdmin(), "include-file-name", ["name" => "Tintin"])
```

> Tintin will execute the templae only if the `!$user->isAdmin()` condition is correct

Disons que le fichier `filename.tintin.php` n'existe pas mais vous souhaitez l'intéger parce que souvent par d'autre moyen ce fichier existe

```t
%includeIf("filename", ["name" => "Tintin"])
```

### Directives d'authentification

Les directives `%auth` et `%guest` peuvent être utilisées pour déterminer rapidement si l'utilisateur actuel est authentifié ou est un invité :

```t
%auth
  // The user is authenticated...
%endauth
 
%guest
  // The user is not authenticated...
%endguest
```

Si nécessaire, vous pouvez spécifier la garde d'authentification qui doit être vérifiée lors de l'utilisation des directives `%auth` et `%guest` :

```t
%auth('admin')
  // The user is authenticated...
%endauth
 
%guest('admin')
  // The user is not authenticated...
%endguest
```

### Environment Guidelines

Vous pouvez vérifier si l'application s'exécute dans l'environnement de production à l'aide de la directive `%production` :

```t
%production
  // Production specific content...
%endproduction
```

Ou, vous pouvez déterminer si l'application s'exécute dans un environnement spécifique à l'aide de la directive `%env` :

```t
%env('staging')
  // The application is running in "staging"...
%endenv

%env(['staging', 'production'])
  // The application is running in "staging" or "production"...
%endenv
```

## Héritage avec %extends, %block et %inject

Comme tout bon système de template **tintin** support le partage de code entre fichier. Ceci permet de rendre votre code flexible et maintenable.

Considérérons le code **tintin** suivant:

```t
# le fichier `layout.tintin.php`
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

Et aussi, on a un autre fichier qui hérite du code du fichier `layout.tintin.php`

```t
# le fichier se nomme `content.tintin.php`
%extends('layout')

%block('content')
  <p>This is the page content</p>
%endblock
```

### Explication

Le fichier `content.tintin.php` va hérité du code de `layout.tintin.php` et si vous rémarquez bien, dans le fichier `layout.tintin.php` on a la clause `%inject` qui a pour paramètre le nom du `%block` de `content.tintin.php` qui est `content`. Ce qui veut dire que le contenu du `%block` `content` sera remplacé par `%inject`. Ce qui donnéra à la fin ceci:

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

## Directive personnelisée

Tintin peut être étendu avec son systême de directive personnalisé, pour ce faire utilisé la méthode `directive`

```php
$tintin->directive('hello', function (string $name) {
  return 'Hello, '. $name;
});

echo $tintin->render('%hello("Tintin")');
// => Hello, Tintin
```

### Exemple

Création de directive pour gérer un formulaires:

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

### Utilisation des directives

Pour utiliser ces directives, rien de plus simple. Ecrivez le nom de la directive précédé la par `%`. Ensuite si cette directive prend des paramètres, lancer la directive comme vous lancez les fonctions dans votre programme.

```t
# File form.tintin.php
%form("/posts", "post", "multipart/form-data")
  %input("text", "name")
  %textarea("content")
  %button('submit', 'Add')
%endform
```

### Compilation du template

La compilation se fait comme d'habitude, pour plus d'information sur la [compilation](#utilisation).

```php
echo $tintin->render('form');
```

### Sortie après compilation

```html
<form action="/posts" method="post" enctype="multipart/form-data">
  <input type="text" name="name" value="" />
  <textarea name="content"></textarea>
  <button type="submit">Add</button>
</form>
```

### Ajouter vos directives de la configuration

Dans le cas ou vous utilisez la configuration Tintin pour Bow Framework.
Changer le vos configuration dans le `ApplicationController::class` dans le dossier `app/Configurations`.

```php
namespace App\Configurations;

use Bow\Configuration\Loader;

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
    $tintin = app('view')->getEngine();

    $tintin->directive('super', function () {
      return "Super !";
    });
  }
}
```

Maintenant la directive `%super` est disponible et vous pouvez l'utiliser.

```php
return $tintin->render('%super');
// => Super !
```

### La directive `%macro`

Souvant, vous serez amener utiliser ou réutiliser un block de template pour optimiser l'écriture de votre application. Alors les macros sont là pour cela
Les macros doivent être définir dans un fichier séparé.

Pour vous utiliser les `%macro` vous devez passer en premier paramêtre le nom du macro et ensuite les paramêtres du macro.

Considérons le fichier `user-macro.tintin.php`.

```t
%macro('users', array $users)
  %loop($users as $user)
    <div>{{ $user }}</div>
  %endloop
%endmacro
```

Pour utiliser le macro vous devez l'importer dans un autre fichier avec `%import`.

Nous allons appeler le fichier `app.tintin.php`.

```t
%import('user-macro')

%extends('layout')

%block('content')
  {{ users($users) }}
%endblock
```

Après compilation du fichier

```php
$users = ["franck", "lucien", "brice"];
$tintin->render('app', compact('users'));
```

Après compilation du fichier

```html
<div>franck</div>
<div>lucien</div>
<div>brice</div>
```
