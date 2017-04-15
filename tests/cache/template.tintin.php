{{ $name }} {{ $lastname }}
{{{ "<div>".$name."</div>" }}}

@if $name == "Franck":
    {{ $name }}
@endif

@loop [$name, $lastname] as $name:
    @jump($name == "Franck")
    @stop($name == "Franck")
    {{ $name }}
@endloop

<?php $i = -10; ?>

@while $i < 0:
    <?php $i++; ?>
@endwhile