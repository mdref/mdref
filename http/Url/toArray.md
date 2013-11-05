# array http\Url::toArray()

Retrieve the URL parts as array.

## Params:

None.

## Returns:

* array, the URL parts.

## Example:

    var_dump((new http\Url)->toArray());

Yields:

    array(7) {
      ["scheme"]=>
      string(4) "http"
      ["user"]=>
      string(0) ""
      ["pass"]=>
      string(0) ""
      ["host"]=>
      string(7) "smugmug"
      ["path"]=>
      string(1) "/"
      ["query"]=>
      string(0) ""
      ["fragment"]=>
      string(0) ""
    }
