# pecl/http v2

> **Note:** This documentation is work-in-progress.

## About:

Extended HTTP support. Again. Keep in mind that it's got the major version 2, because it's incompatible with pecl_http v1.

* Introduces the http namespace.
* Message bodies have been remodeled to use PHP temporary streams instead of in-memory buffers.
* The utterly misunderstood HttpResponse class has been reimplemented as http\Env\Response inheriting http\Message.
* Currently, there's only one Exception class left, http\Exception.
* Errors triggered by the extension can be configured statically by http\Object::$defaultErrorHandling or inherited http\Object->errorHandling.
* The request ecosystem has been modularized to support different libraries, though for the moment only libcurl is supported.

## Installation:

This extension is hosted at PECL (<http://pecl.php.net>) and can be installed eith PEAR's pecl command:

    # pecl install pecl_http

## INI Directives:

* http.etag.mode = "crc32b"  
  Default hash method for dynamic response payloads to generate an ETag.

