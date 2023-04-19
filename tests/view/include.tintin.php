%include("partials.simple")
%includeIf($name == 'bowphp', "partials.include-if")
%includeWhen($logged ?? false, "partials.include-when")