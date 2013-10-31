# void http\QueryString::__construct([mixed $params = NULL])

Create an independent querystring instance.

## Params:

* Optional mixed $params = NULL  
  The query parameters to use or parse.
  
## Throws:

* http\Exception

## Example:

    $qs = new http\QueryString("foo=bar&a[]=1&a[]=2");
    print_r($qs->toArray());

Would yield:

    Array
    (
        [foo] => bar
        [a] => Array
            (
                [0] => 1
                [1] => 2
            )
    
    )
