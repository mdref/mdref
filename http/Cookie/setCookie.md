# http\Cookie http\Cookie::setCookie(string $cookie_name, string $cookie_value)

(Re)set a cookie.
See http\Cookie::addCookie() and http\Cookie::setCookies().

> **Note:** The cookie will be deleted from the list if $cookie_value is NULL.

## Params:

* string $cookie_name  
  The key of the cookie.
* string $cookie_value  
  The value of the cookie.

## Returns:

* http\Cookie, self.

## Throws:

* http\Exception\InvalidArgumentException
