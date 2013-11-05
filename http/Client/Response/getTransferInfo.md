# mixed http\Response::getTransferInfo([string $name = NULL])

Retrieve transfer related information after the request has completed.
See http\Client::getTransferInfo().

## Params:

* Optional string $name = NULL  
  A key to retrieve out of the transfer info.

## Returns:

* array, all transfer info if $name was not given.
* mixed, the specific transfer info for $name.
* false, if the request was not complete or $name was not found.
