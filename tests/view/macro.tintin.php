%macro("greeting", string $name = "name")
    Hello {{ $name }}
%endmacro

%macro("sum", int $number_1, int $number_2)
    Hello {{ $number_1 + $number_2 }}
%endmacro

%macro("user_list", $users)
    <ul>
        %loop($users as $user)
            <li>User's {{ $user }}</li>
        %endloop
    </ul>
%endmacro

%macro('field', $type, $name, $value)
    <input type="{{ $type }}" name="{{ $name }}" value="{{ $value }}"/>
%endmacro
