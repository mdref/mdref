# string http\Client\Request::getQuery()

Retrieve the currently set querystring.

## Params:

None.

## Returns:

* string, the currently set querystring.
* NULL, if no querystring is set.

## Example:

    <?php
    var_dump((new http\Client\Request)->getQuery());
    var_dump((new http\Client\Request("GET", "http://localhost/?foo"))->getQuery());
    ?>

Yields:

    NULL
    string(3) "foo"
