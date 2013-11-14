# string http\Message::getRequestMethod()

Retrieve the request method of the message.
See http\Message::setRequestMethod() and http\Message::getRequestUrl().

## Params:

None.

## Returns:

* string, the request method.
* false, if the message was not of type request.

## Notices:

* E_MESSAGE_TYPE, if the message is not of type request.
