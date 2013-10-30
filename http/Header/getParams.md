# http\Params http\Header::getParams([mixed $ps = ","[, mixed $as = ";"[, mixed $vs = "="[, int $flags = http\Params::PARSE_DEFAULT]]]])

Create a parameter list out of the HTTP header value.

## Params:

* Optional mixed $ps  
  The parameter separator(s).
* Optional mixed $as  
  The argument separator(s).
* Optional mixed  
  The value separator(s).
* Optional int $flags  
  The modus operandi. See http\Params constants.

## Returns:

* http\Params instance
