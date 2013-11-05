# static array http\Client::getAvailableDrivers()

List available drivers.

## Params:

None.

## Returns:

* array, list of supported drivers.

## Example:

    <?php
    var_dump(http\Client::getAvailableDrivers());
    ?>

Yields:

    array(1) {
      [0]=>
      string(4) "curl"
    }
