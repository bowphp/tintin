{# Echo Data #}
Hello, {{ $name }}.
The current UNIX timestamp is {{ time() }}.

{# Echoing Data After Checking For Existence #}
{{ isset($name) ? $name : 'Default' }}
{{ $name or 'Default' }}

{# Displaying Raw Text With Curly Braces #}
#{{ This will not be processed by Blade }}

{# Do not escape data #}
Hello, {!! $name !!}.

{# Escape Data #}
Hello, {{{ $name }}}.

<?php echo $name; ?>
<?= $name; ?>

<?php
    foreach (range(1, 10) as $number) {
        echo $number;
    }
?>

#include('header')

{# Service injection #}

{# PHP open/close tags #}
<div class="container">
    #php
        foreach (range(1, 10) as $number) {
            echo $number;
        }
    #endphp
</div>

{# Inline PHP #}
<div class="container">
    #php(custom_function())
</div>

#include('footer')

{# Define Blade Layout #}
<html>
    <head>
        <title>
            #inject('title') - App Name
        </title>
    </head>
    <body>
        #blocl('sidebar')
            This is the master sidebar.
        #endblock

        <div class="container">
            #include('content')
        </div>
    </body>
</html>

{# Use Blade Layout #}
#extends('layouts.master')

#block('sidebar')
    <p>This is appended to the master sidebar.</p>
#endblock

#block('content')
    <p>This is my body content.</p>
#endblock

{# yield section #}
#inject('section', 'Default Content')

{# If Statement #}
#if (count($records) === 1)
    I have one record!
#elseif (count($records) > 1)
    I have multiple records!
#elif (count($records) > 1)
    I have multiple records!
#else
    I don't have any records!
#endif

<ul class="list #if (count($records) === 1) extra-class #endif">
    <li>This is the first item</li>
    <li>This is the second item</li>
</ul>

#isset($name)
    Hello, {{ $name }}.
#endisset

#unless (Auth::check())
    You are not signed in.
#endunless

{# Loops #}
#for ($i = 0; $i < 10; $i++)
    The current value is {{ $i }}
#endfor

#loop ($users as $user)
    <p>This is user {{ $user->id }}</p>
#endloop

#while (true)
    <p>I'm looping forever.</p>
#endwhile

{# Single line if statement #}
#if($foo === true) <p>Text</p> #endif

{# Quoted blade directive matching #}
<p class="first-class #if($x==true) second-class #endif">Text</p>

{# Complex conditional inline #}
<p class="first-class #if(($x == true) && ($y == "yes")) second-class #endif">Text</p>
