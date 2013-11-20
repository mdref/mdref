# mixed http\Response::getTransferInfo([string $name = NULL])

Retrieve transfer related information after the request has completed.
See http\Client::getTransferInfo().

## Params:

* Optional string $name = NULL  
  A key to retrieve out of the transfer info.

## Returns:

* object, stdClass instance with all transfer info if $name was not given.
* mixed, the specific transfer info for $name.

## Throws:

* http\Exception\InvalidArgumentException
* http\Exception\BadMethodCallException
* http\Exception\UnexpectedValueException
