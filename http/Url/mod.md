# http\Url http\Url::mod(mixed $parts[, int $flags = http\Url::JOIN_PATH|http\Url::JOIN_QUERY)

Clone this URL and apply $parts to the cloned URL.

> **Note:** This method returns a clone (copy) of this instance.

## Params:

* mixed $parts  
  New URL parts.
* Optional int $flags = http\Url::JOIN_PATH|http\Url::JOIN_QUERY  
  Modus operandi of URL construction. See http\Url constants.

## Returns:

* http\Url, clone.

## Throws:


* http\Exception\InvalidArgumentException
* http\Exception\BadUrlException
