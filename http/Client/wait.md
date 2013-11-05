# bool http\Client::wait([float $timeout = 0])

Wait for $timeout seconds for transfers to provide data.
This is the completion call to http\Client::once().

## Params:

* Optional float $timeout = 0  
  Seconds to wait for data on open sockets.

## Returns:

* bool, success.
