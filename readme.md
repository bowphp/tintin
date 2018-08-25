<h1 align="center">
    <img src="https://github.com/bowphp/arts/raw/master/tintin.jpeg" width="150px">
    <br/>Tintin
</h1>

<p align="center">The sample php Template</p>

## Installation

Pour installer le package il sera plus mieux utiliser `composer` qui est gestionnaire de package `php`.

```bash
composer require bowphp/tintin
```

## Utilisation

Vous pouvez utiliser le package simplement, comme ceci. Mais sauf que cette façon de faire ne permet pas d'exploiter le système d'héritage de façon optimal. Utilisez cette façon de faire, seulement si vous voulez tester le package ou pour les petites applications.

```php
require 'vendor/autoload.php';

$tintin = new Tintin\Tintin;

echo $tintin->render('Hello, world {{ strtoupper($name) }}', ['name' => 'tintin']);
// -> Hello, world TINTIN
```

Pour utiliser proprement le package, il faut suivre plutôt l'installation qui suivant:

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
| __php__ | Le chemin vers le dossier des vues de votre applications |
| __extension__ | l'extension des fichiers de template. Par defaut, la valeur est `tintin.php` |
| __cache__ | Le dossier de cache. C'est là que `tintin` va créé le cache. S'il n'est pas défini, `tintin` mettra en cache les fichiers compilés dans le répertoire temporaire de `php`.  |

Exemple d'utilisation:

```php
// Configuration faite qu préalabe
$tt = new Tintin\Tintin($loader);

$tt->render('filename', ['name' => 'data']);
// Ou
$tt->render('dossier/filename', ['name' => 'data']);
// Ou
$tt->render('dossier.filename', ['name' => 'data']);
```

> Notez que la source des fichiers est toujour le chemin vers `path`.

## Ajouter un commentaire

Cette clause `{# comments #}` permet d'ajouter un commentaire à votre code `tintin`.

## `#if` / `#elseif` / `#else` 

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

## `#unless`

Petite spécificité, le `#unless` quant à lui, il permet de faire une condition inverse du `#if`.
Pour faire simple, voici un exemple:

```
#unless ($name == 'tintin') => #if (!($name == 'tintin'))
```

## `#loop` comme `foreach` / `#for`, `#while`

Souvent vous pouvez être amener à faire des listes ou répétitions sur des éléments. Par exemple, afficher tout les utilisateurs de votre plateforme.

### L'utilisation de `#loop`

Cette clause faire exactement l'action de `foreach`.

```
#loop ($names as $name)
  Bonjour {{ $name }}
#endloop
```

Cette clause peux être aussi coupler avec tout autre clause telque `#if`.
Un exemple rapide.

```
#loop ($names as $name)
  #if ($name == 'tintin')
    Bonjour {{ $name }}
    #stop
  #endif
#endloop
```

Vous avez peut-être remarquer le `#stop` il permet de stoper l'éxécution de la boucle. Il y a aussi son conjoint le `#jump`, lui parcontre permet d'arrêter l'éxécution à son niveau et de lancer s'éxécution du prochain tour de la boucle.

### Les sucres syntaxiques `#jump(condition)` et `#stop(condition)`

Souvent le dévéloppeur est amené à faire des conditions d'arrêt de la boucle `#loop` comme ceci:

```
#loop ($names as $name)
  #if ($name == 'tintin')
    #stop
    // Ou
    #jump
  #endif
#endloop
```

Avec les sucres syntaxique, on peut réduire le code comme ceci:

```
#loop ($names as $name)
  #stop($name == 'tintin')
  // Ou
  #jump($name == 'tintin')
#endloop
```

### L'utilisation de `#for`

Cette clause faire exactement l'action de `for`.

```
#for ($i = 0; $i < 10; $i++)
 // ..
#endfor
```

### L'utilisation de `#while`

Cette clause faire exactement l'action de `while`.

```
#while ($name != 'tintin')
 // ..
#endwhile
```

## `include`

Souvent lorsque vous dévéloppez votre code, vous êtes amener à subdiviser les vues de votre application pour être plus flexible et écrire moin de code.

`include` permet d'include un autre fichier de template dans un autre.

```
 #include('filename')
```

## Héritage avec `#extends`, `#block` et `#inject` 

Comme tout bon système de temple **tintin** support le partage de code entre fichier. Ceci permet de rendre votre code flexible et maintenable.

Considérérons le code **tintin** suivant:
```
// le fichier `layout.tintin.php`
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

Et aussi, on a un autre fichier qui hérite du code du fichier `layout.tintin.php`
```
// le fichier se nomme `content.tintin.php`
#extends('layout')

#block('content')
  <p>This is the page content</p>
#endblock
```

### Explication

Le fichier `content.tintin.php` va hérité du code de `layout.tintin.php` et si vous rémarquez bien, dans le fichier `layout.tintin.php` on a la clause `#inject` qui a pour paramètre le nom du `#block` de `content.tintin.php` qui est `content`. Ce qui veut dire que le contenu du `#block` `content` sera remplacé par `#inject`. Ce qui donnéra à la fin ceci:

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

Pour participer au projet il faut:

+ Fork le projet afin qu'il soit parmi les répertoires de votre compte github ex :`https://github.com/votre-compte/app`
+ Cloner le projet depuis votre compte github `git clone https://github.com/votre-crompte/tintin`
+ Créer un branche qui aura pour nom le résumé de votre modification `git branch branche-de-vos-traveaux`
+ Faire une publication sur votre dépot `git push origin branche-de-vos-traveaux`
+ Enfin faire un [pull-request](https://www.thinkful.com/learn/github-pull-request-tutorial/Keep-Tabs-on-the-Project#Time-to-Submit-Your-First-PR)

Ou bien allez dans la page des [issues](https://github.com/bowphp/tintin/issues), faites vos corrections et enfin suivez [publier](#contribution).

> SVP s'il y a un bogue sur le projet veuillez me contacter sur mon [slack](https://papac.slack.com)