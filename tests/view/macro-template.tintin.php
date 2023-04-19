%import("macro")

%extends('layout')

%block('content')
    {{ greeting("Papac") }}
    {{ sum(1, 2) }}
    {{{ show_users(["Franck", "Brice", "Lucien"]) }}}
    {{{ field('text', 'name', 'papac') }}}
%endblock
