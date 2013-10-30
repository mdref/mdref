# static array http\Header::parse(string $header[, string $header_class = null])

Parse HTTP headers.

## Params:

* string $header  
  The complete string of headers.
* Optional string $header_class  
  A class extending http\Header.
  
## Returns:

* array of parsed headers, where the elements are instances of $header_class if specified.
