# http\Message http\Message::setRequestMethod(string $method)

Set the request method of the message.
See http\Message::getRequestMethod() and http\Message::setRequestUrl().

## Params:

* string $method  
  The request method.

## Returns:

* http\Message, self.
* false, if the message was not of type request.

## Notices:

* E_MESSAGE_TYPE, if the message is not of type request.

## Warnings:

* E_INVALID_PARAM, if the method is of zero length.
