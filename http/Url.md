# class http\Url extends http\Object

The http\Url class provides versatile means to parse, construct and manipulate URLs.

## Constants:

* REPLACE  
  Replace parts of the old URL with parts of the new.
* JOIN_PATH  
  Whether a relative path should be joined into the old path.
* JOIN_QUERY  
  Whether the querystrings should be joined.
* STRIP_USER  
  Strip the user information from the URL.
* STRIP_PASS  
  Strip the password from the URL.
* STRIP_AUTH  
  Strip user and password information from URL (same as STRIP_USER|STRIP_PASS).
* STRIP_PORT  
  Do not include the port.
* STRIP_PATH  
  Do not include the URL path.
* STRIP_QUERY  
  Do not include the URL querystring.
* STRIP_FRAMGENT  
  Strip the fragment (hash) from the URL.
* STRIP_ALL  
  Strip everything except scheme and host information.
* FROM_ENV  
  Import initial URL parts from the SAPI environment.
* SANITIZE_PATH  
  Whether to sanitize the URL path (consolidate double slashes, directory jumps etc.)


## Properties:

* public $scheme = NULL  
  The URL's scheme.
* public $user = NULL  
  Authenticating user.
* public $pass = NULL  
  Authentication password.
* public $host = NULL  
  Hostname/domain.
* public $port = NULL  
  Port.
* public $path = NULL  
  URL path.
* public $query = NULL  
  URL querystring.
* public $fragment = NULL  
  URL fragment (hash).
