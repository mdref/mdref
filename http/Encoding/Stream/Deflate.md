# class http\Encoding\Stream\Deflate extends http\Encoding\Stream

A deflate stream supporting deflate, zlib and gzip encodings.

## Constants:

* TYPE_GZIP  
  Gzip encoding. RFC1952
* TYPE_ZLIB  
  Zlib encoding. RFC1950
* TYPE_RAW  
  Deflate encoding. RFC1951
* LEVEL_DEF  
  Default compression level.
* LEVEL_MIN  
  Least compression level.
* LEVEL_MAX  
  Greatest compression level.
* STRATEGY_DEF  
  Default compression strategy.
* STRATEGY_FILT  
  Filtered compression strategy.
* STRATEGY_HUFF  
  Huffman strategy only.
* STRATEGY_RLE  
  Run-length encoding strategy.
* STRATEGY_FIXED  
  Encoding with fixed Huffman codes only.

> **A note on the compression strategy:**
>
> The strategy parameter is used to tune the compression algorithm.
>
> Use the value DEFAULT_STRATEGY for normal data, FILTERED for data produced by a filter (or predictor), HUFFMAN_ONLY to force Huffman encoding only (no string match), or RLE to limit match distances to one (run-length encoding).
>
> Filtered data consists mostly of small values with a somewhat random distribution. In this case, the compression algorithm is tuned to compress them better. The effect of FILTERED is to force more Huffman coding and less string matching; it is somewhat intermediate between DEFAULT_STRATEGY and HUFFMAN_ONLY.
>
> RLE is designed to be almost as fast as HUFFMAN_ONLY, but give better compression for PNG image data.
> 
> FIXED prevents the use of dynamic Huffman codes, allowing for a simpler decoder for special applications.
>
> The strategy parameter only affects the compression ratio but not the correctness of the compressed output even if it is not set appropriately. 
>
>_Source: [zlib Manual](http://www.zlib.net/manual.html)_


## Properties:

None.
