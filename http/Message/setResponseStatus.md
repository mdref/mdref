# http\Message http\Message::setResponseStatus(string $response_status)

Set the response status phrase.
See http\Message::getResponseStatus() and http\Message::setResponseCode().

## Params:

* string $response_status  
  The status phrase.

## Returns:

* http\Message, self.
* false, if the message is not of type response.

## Notice:

* E_MESSAGE_TYPE, if the message is not of type response.


## Example:

    <?php
    $message = new http\Message;
    $message->setType(http\Message::TYPE_RESPONSE);
    $message->setResponseCode(200);
    $message->setResponseStatus("Ok");
    echo $message;
    ?>

Yields:

    HTTP/1.1 200 Ok
