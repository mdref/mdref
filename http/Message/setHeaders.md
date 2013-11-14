# http\Message http\Message::setHeaders(array $headers = NULL)

Set the message headers.
See http\Message::getHeaders() and http\Message::addHeaders().

## Params:

* array $headers = NULL  
  The message's headers.

## Returns:

* http\Message, null.

## Example:

    <?php
    $msg = new http\Message;
    
    $msg->setHeaders([
        "Content-Type" => "text/plain",
        "Content-Encoding" => "gzip",
        "Content-Location" => "/foo/bar"
    ]);
    var_dump($msg->getHeaders());
    
    $msg->setHeaders(null);
    var_dump($msg->getHeaders());
    ?>

Yields:

    array(3) {
      ["Content-Type"]=>
      string(10) "text/plain"
      ["Content-Encoding"]=>
      string(4) "gzip"
      ["Content-Location"]=>
      string(8) "/foo/bar"
    }
    array(0) {
    }

