%import("macro")

%extends('layout')

%block('content')
    {{ greeting("Papac") }}
    {{{ user_list(["Franck", "Brice", "Lucien"]) }}}
    {{{ field('text', 'name', 'papac') }}}
    {{ sum(1, 2) }}
%endblock
