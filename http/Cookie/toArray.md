# array http\Cookie::toArray()

Get the cookie list as array.

## Params:

None.

## Returns:

* array, the cookie list as array.

## Example:

    <?php
    $cookie = new http\Cookie("country=us; secure");
    var_dump($cookie->toArray());
    ?>

Yields:

    array(7) {
      ["cookies"]=>
      array(1) {
        ["country"]=>
        string(2) "us"
      }
      ["extras"]=>
      array(0) {
      }
      ["flags"]=>
      int(16)
      ["expires"]=>
      int(-1)
      ["max-age"]=>
      int(-1)
      ["path"]=>
      string(0) ""
      ["domain"]=>
      string(0) ""
    }
