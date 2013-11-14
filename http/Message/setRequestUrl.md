# http\Message http\Message::setRequestUrl(string $url)

Set the request URL of the message.
See http\Message::getRequestUrl() and http\Message::setRequestMethod().

## Params:

* string $url  
  The request URL.

## Returns:

* http\Message, self.
* false, if the message was not of type request.

## Notices:

* E_MESSAGE_TYPE, if the message is not of type request.

## Warnings:

* E_INVALID_PARAM, if the url is of zero length.

## Example:

> **Note:** The request URL in a request message usually only consists of the path and the querystring.

    <?php
    $m = new http\Message;
    $m->setType(http\Message::TYPE_REQUEST);
    $m->setRequestMethod("GET");
    $m->setRequestUrl("http://foo.bar/baz?q");
    echo $m;
    ?>

Yields:

    GET http://foo.bar/baz?q HTTP/1.1

Maybe you did not really expect this, so let's try this:

    <?php
    $m = new http\Message;
    $u = new http\Url("http://foo.bar/baz?q");
    $m->setType(http\Message::TYPE_REQUEST);
    $m->setRequestMethod("GET");
    $m->setRequestUrl($u->path ."?". $u->query);
    $m->setHeader("Host", $u->host);
    echo $m;
    ?>

Yields:

    GET /baz?q HTTP/1.1
    Host: foo.bar
