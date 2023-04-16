%import("macro")

%extends('layout')

%block('name')
    {{ greeting("papac") }}
%endblock
