# static string http\Encoding\Stream\Deflate::encode(string $data[, int $flags = 0])

Encode data with deflate/zlib/gzip encoding.

## Params:

* string $data  
  The data to compress.
* Optional int $flags = 0  
  Any compression tuning flags. See http\Encoding\Stream\Deflate and http\Encoding\Stream constants.

## Returns:

* string, the compressed data.
