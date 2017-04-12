<?php
require __DIR__.'/../vendor/autoload.php';

use Tintin\Compiler;

$compile = new Compiler();

$output = $compile->complie(file(__DIR__.'/cache/template.tintin.php'));
$output = "<?php \$name = \"Franck\"; \$lastname = \"Dakia\"; ?>\n" . $output;

function e($string)
{
    return stripslashes(htmlspecialchars($string));
}

file_put_contents(__DIR__.'/cache/'.sha1('test').'.php', $output);
require __DIR__.'/cache/'.sha1('test').'.php';