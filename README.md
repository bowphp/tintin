# tintin
The sample php Template

## Installation
```bash
composer require papac/tintin
```

# Configuration

```php
require 'vendor/autoload.php';
$loader = new Tintin\Loader\Filesystem(__DIR__.'/views');
$tintin = new Tintin\Tinitin($loader, [
  'cache' => true,
  'cache_dir' => '/path/to/the/cache/directory',
  'extension' => 'tt',
  'expire' => ture
]);
echo $tintin->render('Hello {{name}}', ['name' => 'tintin']);
// -> Hello tintin
```

## Usage

### Condiction

```jinja2
@if name == 'tintin':
  {{ name }}
@elseif name == 'template':
  {{ name }}
@else:
  {{ name }}
@endif
```

### Loop

```jinja2
@loop $names as $name:
  
  @if $name == 'tintin':
    ...
  @endif
  
@endloop
```

### Inclusion

```jinja2
 @include('filename')
```
