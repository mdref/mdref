# class http\Env\Response extends http\Message is callable

The http\Env\Response class' instances represent the server's current HTTP response.

See http\Message for inherited members.

## Constants:

* CONTENT_ENCODING_NONE  
  Do not use content encoding.
* CONTENT_ENCODING_GZIP  
  Support "Accept-Encoding" requests with gzip and deflate encoding.
* CACHE_NO  
  No caching info available.
* CACHE_HIT  
  The cache was hit.
* CACHE_MISS  
  The cache was missed.

## Properties:

* protected $request = NULL  
  A http\Env\Request instance which overrides the environments default request.
* protected $contentType = NULL  
  The response's MIME content type.
* protected $contentDisposition = NULL  
  The response's MIME content disposition.
* protected $contentEncoding = NULL  
  See http\Env\Response::CONTENT_ENCODING_* constants.
* protected $cacheControl = NULL  
  How the client should treat this response in regards to caching.
* protected $etag = NULL  
  A custom ETag.
* protected $lastModified = NULL  
  A "Last-Modified" time stamp.
* protected $throttleDelay = NULL  
  Any throttling delay.
* protected $throttleChunk = NULL  
  The chunk to send every $throttleDelay seconds.
