# http\Client http\Client::enablePipelining([bool $enable = true])

Enable sending pipelined requests to the same host if the driver supports it.

## Params:

* Optional bool $enable = true  
  Whether to enable pipelining.

## Returns:

* http\Client, self.

## Throws:

* http\Exception\InvalidArgumentException
* http\Exception\UnexpectedValueException
