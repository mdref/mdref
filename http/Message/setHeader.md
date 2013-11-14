# http\Message http\Message::setHeader(string $header[, mixed $value = NULL])

Set a single header.
See http\Message::getHeader() and http\Message::addHeader().

## Params:

* string $header  
  The header's name.
* Optional mixed $value = NULL  
  The header's value. Removes the header if NULL.

## Returns:

* http\Message, self.
