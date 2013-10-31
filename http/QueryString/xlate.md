# http\QueryString http\QueryString::xlate($from_enc, $to_enc)

Translate character encodings of the querystring with ext/iconv.

> **Note:** This method is only available when ext/iconv support was enabled at build time.

## Params:

* string $from_enc  
  The encoding to convert from.
* string $to_enc  
  The encoding to convert to.

## Returns:

* http\QueryString, self.

## Throws:

* http\Exception
