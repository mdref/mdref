# string http\Message::getResponseStatus()

Retrieve the response status of the message.
See http\Message::setResponseStatus() and http\Message::getResponseCode().

## Params:

None.

## Returns:

* string, the response status phrase.
* false, if the message is not of type response.

## Notices:

* E_MESSAGE_TYPE, if the message is not of type response.
