%include("partials.simple")
%includeif($name == 'bowphp', "partials.include-if")
%includewhen($logged ?? false, "partials.include-when")