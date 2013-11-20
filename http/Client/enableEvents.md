# http\Client http\Client::enableEvents([bool $enable = true])

Enable usage of an event library like libevent, which might improve performance with big socket sets.

## Params:

* Optional bool $enable = true  
  Whether to enable libevent usage.

## Returns:

* http\Client, self.

## Throws:

* http\Exception\InvalidArgumentException
* http\Exception\UnexpectedValueException
