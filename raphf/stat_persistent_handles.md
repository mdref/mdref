# object raphf\stat_persistent_handles()

Retrieve statistics about current process'/thread's persistent handles.

## Params:

None.

## Returns:

* object, stdClass instance


## Example:

    var_dump(raphf\stat_persistent_handles());

Yields:

    object(stdClass)#6 (3) {
        ["http\Client\Curl"]=>
        array(0) {
        }
        ["http\Client\Curl\Request"]=>
        array(1) {
        ["php.net:80"]=>
        array(2) {
          ["used"]=>
          int(2)
          ["free"]=>
          int(1)
        }
        }
        ["pq\Connection"]=>
        array(0) {
        }
    }
