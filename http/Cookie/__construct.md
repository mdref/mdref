# void http\Cookie::__construct([mixed $cookies = NULL[, int $flags = 0[, array $allowed_extras = NULL]]])

Create a new cookie list.

## Params:

* Optional mixed $cookies = NULL  
  The string or list of cookies to parse or set.
* Optional int $flags = 0  
  Parse flags. See http\Cookie::PARSE_* constants.
* Optional array $allowed_extras = NULL  
  List of extra attribute names to recognize.

## Throws:

* http\Exception\InvalidArgumentException
* http\Exception\RuntimeException

## Example:

    <?php
    $cookie = new http\Cookie("c1=v1; c2=v2; extra=foo; ".
        "expires=Thu, Nov  7 2013 10:00:00 GMT; path=/a/b/c", 
        0, ["extra"]);
    var_dump([
        "cookies" => $cookie->getCookies(),
        "extras" => $cookie->getExtras(),
        "expires" => $cookie->getExpires(),
        "max-age" => $cookie->getMaxAge(),
        "domain" => $cookie->getDomain(),
        "path" => $cookie->getPath(),
        "flags" => $cookie->getFlags(),
        "string" => (string) $cookie
    ]);
    ?>

Yields:

    array(8) {
      ["cookies"]=>
      array(2) {
        ["c1"]=>
        string(2) "v1"
        ["c2"]=>
        string(2) "v2"
      }
      ["extras"]=>
      array(1) {
        ["extra"]=>
        string(3) "foo"
      }
      ["expires"]=>
      int(1383818400)
      ["max-age"]=>
      int(-1)
      ["domain"]=>
      NULL
      ["path"]=>
      string(6) "/a/b/c"
      ["flags"]=>
      int(0)
      ["string"]=>
      string(77) "c1=v1; c2=v2; path=/a/b/c; expires=Thu, 07 Nov 2013 10:00:00 GMT; extra=foo; "
    }
