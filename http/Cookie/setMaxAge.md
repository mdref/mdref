# http\Cookie http\Cookie::setMaxAge([int $value = -1])

Set the maximum age the cookie may have on the client side.
This is a client clock departure safe alternative to the "expires" attribute.
See http\Cookie::setExpires().

## Params:

* Optional int $value = -1  
  The max-age in seconds.

## Returns:

* http\Cookie, self.

## Throws:

* http\Exception\InvalidArgumentException
