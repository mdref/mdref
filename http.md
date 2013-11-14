# pecl/http v2

## About:

Extended HTTP support. Again. 

* Introduces the http namespace.
* PHP stream based message bodies.
* Encapsulated env request/response.
* Versatile error handling.
* Modular client support.

## Installation:

This extension is hosted at [PECL](http://pecl.php.net) and can be installed with [PEAR](http://pear.php.net)'s pecl command:

    # pecl install pecl_http

## Dependencies:

This extension unconditionally depends on the pre-loaded presence of the following PHP extensions:

* raphf
* propro
* spl


If configured ```--with-http-shared-deps``` (default) it requires on the pre-loaded presence of the following extensions, as long as they where available at build time:

* hash
* iconv
* json

## Conflicts:

pecl/http-v2 conflicts with thw following extensions:

* http-v1
* event

## INI Directives:

* http.etag.mode = "crc32b"  
  Default hash method for dynamic response payloads to generate an ETag.

## Stream Filters:

The http extension registers the ```http.*``` namespace for its stream filters. Provided stream filters are:

* http.chunked_decode  
  Decode a stream encoded with chunked transfer encoding.
* http.chunked_encode  
  Encode a stream with chunked transfer encoding.
* http.inflate  
  Decode a stream encoded with deflate/zlib/gzip encoding.
* http.deflate  
  Encode a stream with deflate/zlib/gzip encoding.
