%include("partials.simple")
%includeIf("partials.include-if-not-exists")
%includeIf("partials.include-if")
%includeWhen($logged ?? false, "partials.include-when")