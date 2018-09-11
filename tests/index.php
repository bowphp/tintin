<?php

require __DIR__.'/../vendor/autoload.php';

$tintin = new Tintin\Tintin;

$template = <<<TEMPLATE
Hello, world {{ strtoupper(\$name) }}
TEMPLATE;

echo $tintin->render($template, ['name' => 'tintin']);
