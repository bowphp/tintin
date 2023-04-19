%macro("greeting", string $name = "name")
    Hello, {{ $name }}
%endmacro

%macro("sum", int $a, int $b)
    Sum of {{ $a }} + {{ $b }} = {{ $b + $a }}
%endmacro

%macro("show_users", $users)
    %loop($users as $user)
        <div>User's {{ $user }}</div>
    %endloop
%endmacro

%macro('field', $type, $name, $value)
    <input type="{{ $type }}" name="{{ $name }}" value="{{ $value }}"/>
%endmacro
