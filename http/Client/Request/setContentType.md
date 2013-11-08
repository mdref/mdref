# http\Client\Request http\Client\Request::setContentType(string $content_type)

Set the MIME content type of the request message.

## Params:

* string $content_type  
  The MIME type used as "Content-Type".

## Returns:

* http\Client\Request, self.

## Warnings:

* HTTP_E_INVALID_PARAM, if $content_type does not follow the general "primary/secondary" notation.
