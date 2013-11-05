# string http\Cookie::__toString()

String cast handler. Alias of http\Cookie::toString().

## Params:

None.

## Returns:

* string, the cookie(s) represented as string.

## Example:

    <?php
    echo new http\Cookie("country=us; httpOnly; secure; path=/; domain=.php.net");
    ?>

Yields:

    country=us; domain=.php.net; path=/; secure; httpOnly; 
