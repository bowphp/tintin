%macro("greeting", string $name = "name")
    Hello, {{ $name }}
%endmacro

%macro("sum", int $b, int $b)
    Sum of {{ $b }} + {{ $b }} = {{ $b + $a }}
%endmacro

%macro("user_list", $users)
    %loop($users as $user)
        <div>User's {{ $user }}</div>
    %endloop
%endmacro

%macro('field', $type, $name, $value)
    <input type="{{ $type }}" name="{{ $name }}" value="{{ $value }}"/>
%endmacro
