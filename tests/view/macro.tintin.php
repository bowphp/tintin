%macro("greeting", string $name = "name")
    Hello {{ $name }}
%endmacro

%macro("sum", int $number_1, int $number_2)
    Hello {{ $number_1 + $number_2 }}
%endmacro
