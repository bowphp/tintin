<?php
    $name = 'Tintin';
?>

@raw
    if ($name == 'Tintin') {
        {{ $name }}
    }
@endraw

{{ $name }} {{ $lastname }}

{{{ "<div>".$name."</div>" }}}

@if ($name == "Franck"):
    {{ $name }}
@endif

@loop([$name, $lastname] as $name):
    {{ $name }}
@endloop