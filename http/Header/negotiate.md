# string http\Header::negotiate(array $supported[, array &$result])

Negotiate the header's value against a list of supported values in $supported. 
Negotiation operation is adopted according to the header name, i.e. if the 
header being negotiated is Accept, then a slash is used as primary type 
separator, and if the header is Accept-Language respectively, a hyphen is 
used instead.

> **Note:** The first elemement of $supported serves as a default if no operand matches.

## Params:

* array $supported  
  The list of supported values to negotiate.
* Optional reference array &$result
  Out parameter recording the negotiation results.
  
## Returns:

* NULL, if negotiation fails.
* string, the closest match negotiated, or the default (first entry of $supported).
