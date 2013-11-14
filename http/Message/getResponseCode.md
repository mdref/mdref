# int http\Message::getResponseCode()

Retrieve the response code of the message.
See http\Message::setResponseCode() and http\Massage::getResponseStatus().

## Params:

None.

## Returns:

* int, the response status code.
* false, if the message is not of type response.

## Notices:

* E_MESSAGE_TYPE, if the message is not of type response.
