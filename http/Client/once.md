# bool http\Client::once()

Perform outstanding transfer actions.
See http\Client::wait() for the completing interface.

## Params:

None.

## Returns:

* bool, true if there are more transfers to complete.

## Example:

    <?php
    $client = new http\Client;
    $client->enqueue(new http\Client\Request("HEAD", "http://php.net"));
    $client->enqueue(new http\Client\Request("HEAD", "http://pecl.php.net"));
    $client->enqueue(new http\Client\Request("HEAD", "http://pear.php.net"));
    
    printf("Transfers ongoing");
    while ($client->once()) {
        // do something else here while the network transfers are busy
        printf(".");
        // and then call http\Client::wait() to wait for new input
        $client->wait();
    }
    printf("\n");
    
    while ($res = $client->getResponse()) {
        printf("%s returned %d\n", $res->getTransferInfo("effective_url"),
            $res->getResponseCode());
    }

Yields:

    Transfers ongoing....................................................
    http://php.net/ returned 200
    http://pecl.php.net/ returned 200
    http://pear.php.net/ returned 200
