# mixed http\Client\Response::getCookies([int $flags = 0[, array $allowed_extras = NULL]])

Extract response cookies.
Parses any "Set-Cookie" response headers into an http\Cookie list. See http\Cookie::__construct().

## Params:

* Optional int $flags = 0  
  Cookie parser flags.
* Optional array $allowed_extras = NULL  
  List of keys treated as extras.

## Returns:

* array, list of http\Cookie instances.


## Example:

    <?php
    $response = (new http\Client)
        ->enqueue(new http\Client\Request("GET", "http://php.net/"))
        ->send()
        ->getResponse();
    
    foreach ($response->getCookies() as $cookie) {
        /* @var $cookie http\Cookie */
        foreach ($cookie->getCookies() as $name => $value) {
            printf("Got a cookie named '%s' with value '%s'\n\tdomain=%s\n\t  path=%s\n", 
                $name, $value, $cookie->getDomain(), $cookie->getPath());
        }
    }
    ?>

Yields:

    Got a cookie named 'COUNTRY' with value 'USA,72.52.91.14'
        domain=.php.net
          path=/
