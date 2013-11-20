# http\Message http\Client::getHistory()

Simply returns the http\Message chain representing the request/response history.

> **Note:** The history is only recorded while http\Client::$recordHistory is true.

## Params:

None.

## Returns:

* http\Message, the request/response message chain representing the client's history.

## Throws:

* http\Exception\InvalidArgumentException

## Example:

    <?php
    $client = new http\Client;
    $client->enqueue(new http\Client\Request("HEAD", "http://php.net"));
    $client->enqueue(new http\Client\Request("HEAD", "http://pecl.php.net"));
    $client->enqueue(new http\Client\Request("HEAD", "http://pear.php.net"));
    $client->recordHistory = true;
    $client->send();
    
    echo $client->getHistory()->toString(true);

Yields:

    HEAD / HTTP/1.1
    User-Agent: PECL::HTTP/2.0.0dev (PHP/5.5.5)
    Host: pear.php.net
    Accept: */*
    Content-Length: 0

    HTTP/1.1 200 OK
    Date: Mon, 04 Nov 2013 15:41:48 GMT
    Server: ...
    X-Powered-By: PHP/5.3.6
    Connection: close
    Content-Type: text/html; charset=UTF-8
    Content-Length: 0

    HEAD / HTTP/1.1
    User-Agent: PECL::HTTP/2.0.0dev (PHP/5.5.5)
    Host: pecl.php.net
    Accept: */*
    Content-Length: 0

    HTTP/1.1 200 OK
    Date: Mon, 04 Nov 2013 14:34:02 GMT
    Server: ...
    X-Powered-By: PHP/5.2.17
    Set-Cookie: ...
    Expires: Thu, 19 Nov 1981 08:52:00 GMT
    Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0
    Pragma: no-cache
    Connection: close
    Content-Type: text/html; charset=ISO-8859-1
    Content-Length: 0

    HEAD / HTTP/1.1
    User-Agent: PECL::HTTP/2.0.0dev (PHP/5.5.5)
    Host: php.net
    Accept: */*
    Content-Length: 0

    HTTP/1.1 200 OK
    Server: ...
    Date: Mon, 04 Nov 2013 14:34:08 GMT
    Content-Type: text/html;charset=utf-8
    Connection: keep-alive
    X-Powered-By: PHP/5.5.4-1
    Content-Language: en
    Set-Cookie: ...
    Last-Modified: Mon, 04 Nov 2013 21:00:36 GMT
    Content-Length: 0
