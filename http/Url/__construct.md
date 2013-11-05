# void http\Url::__construct([mixed $old_url = NULL[, mixed $new_url = NULL[, int $flags = http\Url::FROM_ENV]]])

Create an instance of an http\URL.

## Params:

* Optional mixed $old_url = NULL  
  Initial URL parts. Either an array, object, http\Url instance or string to parse.
* Optional mixed $new_url = NULL  
  Overriding URL parts. Either an array, object, http\Url instance or string to parse.
* Optional int $flags = http\Url::FROM_ENV  
  The modus operandi of constructing the url. See http\Url constants.

## Throws:

* http\Exception

