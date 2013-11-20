# http\Client\Request http\Client\Request::setQuery([mixed $query_data = NULL])

(Re)set the querystring.
See http\Client\Request::addQuery() and http\Message::setRequestUrl().

## Params:

* mixed $query_data, new querystring data.

## Returns:

* http\Client\Request, self.

## Throws:

* http\Exception\InvalidArgumentException
* http\Exception\BadQueryStringException

## Example:

    <?php
    $q = new http\QueryString("foo=bar&bar=foo");
    $r = new http\Client\Request;
    $r->setQuery($q);
    var_dump($r->getRequestUrl());
    ?>

Yields:

    string(33) "http://localhost/?foo=bar&bar=foo"
