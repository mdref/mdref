# static string http\Encoding\Stream\Dechunk::decode(string $data[, int &$decoded_len = 0])

Decode chunked encoded data.

## Params:

* string $data  
  The data to decode.
* Optional reference int $decoded_len = 0  
  Out parameter with the length of $data that's been decoded.
  Should be ```strlen($data)``` if not truncated.

## Returns:

* string, the decoded data.
* string, the unencoded data.
* string, the truncated decoded data.
* false, if $data cannot be decoded.

## Notices:

* If $data does not seem to be chunked encoded.

## Warnings:

* If $data cannot be decoded or is truncated.
