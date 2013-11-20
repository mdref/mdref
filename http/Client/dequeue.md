# http\Client http\Client::dequeue(http\Client\Request $request)

Dequeue the http\Client\Request $request.

See http\Client::requeue(), if you want to requeue the request, instead of calling http\Client::dequeue() and then http\Client::enqueue().

## Params:

* http\Client\Request $request  
  The request to cancel.

## Returns:

* http\Client, self.

## Throws:

* http\Exception\InvalidArgumentException
* http\Exception\BadMethodCallException
* http\Exception\RuntimeException
