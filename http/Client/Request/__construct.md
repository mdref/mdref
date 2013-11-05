# void http\Client\Request::__construct([string $meth = NULL[, string $url = NULL[, array $headers = NULL[, http\Message\Body $body = NULL]]]])

Create a new client request message to be enqueued and sent by http\Client.

## Params:

* Optional string $meth = NULL  
  The request method.
* Optional string $url = NULL  
  The request URL.
* Optional array $headers = NULL  
  HTTP headers.
* Optional http\Message\Body $body = NULL  
  Request body.

## Throws:

* http\Exception.

## Example:

    <?php
    $request = new http\Client\Request("GET", "http://php.net/");
    ?>
