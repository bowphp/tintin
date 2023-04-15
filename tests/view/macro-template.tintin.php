%import("macro")

%extends('layout')

%block('name')
    Hello {{ greeting("papac") }}
%endblock

$macros["greeting"] = function (int $number_1, int $number_2) {
    return "Hello {{ $number_1 + $number_2 }}\n";
};

function_exists("greeting");

return call_user_func_array($macros["greeting"], [$number_1, $number_2]);