# bool http\Header::match(string $value[, int $flags = http\Header::MATCH_LOOSE])

Match the HTTP header's value against provided $value according to $flags.

## Params:

* string $value  
  The comparison value.
* Optional int $flags  
  The modus operandi. See http\Header constants.
  
## Returns:

* bool, whether $value matches the header value according to $flags.
