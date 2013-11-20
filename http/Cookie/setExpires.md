# http\Cookie http\Cookie::setExpires([int $value = -1])

Set the traditional expires timestamp.
See http\Cookie::setMaxAge() for a safer alternative.

## Params:

* Optional int $value = -1  
  The expires timestamp as seconds since the epoch.

## Returns:

* http\Cookie, self.

## Throws:

* http\Exception\InvalidArgumentException
