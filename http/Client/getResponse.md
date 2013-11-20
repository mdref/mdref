# http\Client\Response http\Client::getResponse([http\Client\Request $request = NULL])

Retrieve the corresponding reponse of an already finished request, or the last received response if $request is not set.

> **Note:** If $request is NULL, then the response is removed from the internal storage (stack-like operation).

## Params:

* Optional http\Client\Request $request  
  The request to fetch the stored response for.

## Returns:

* http\Client\Response, the stored response for the request, or the last that was received.
* NULL, if no more response was available to pop, when no $request was given.

## Throws:

* http\Exception\InvalidArgumentException
* http\Exception\UnexpectedValueException

## Example:

    <?php
    $client = new http\Client;
    $client->enqueue(new http\Client\Request("GET", "http://php.net"));
    $client->enqueue(new http\Client\Request("GET", "http://pecl.php.net"));
    $client->enqueue(new http\Client\Request("GET", "http://pear.php.net"));
    $client->send();
    
    while ($res = $client->getResponse()) {
        printf("%s returned %d\n", $res->getTransferInfo("effective_url"),
            $res->getResponseCode());
    }

Yields:

    http://php.net/ returned 200
    http://pecl.php.net/ returned 200
    http://pear.php.net/ returned 200

