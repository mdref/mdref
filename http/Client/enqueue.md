# http\Client http\Client::enqueue(http\Client\Request $request[, callable $cb])

Add another http\Client\Request to the request queue.
If the optional callback $cb returns true, the request will be automatically dequeued.

See http\Client::dequeue() and http\Client::send().

## Params:

* http\Client\Request $request  
  The request to enqueue.
* Optional callable $cb  
  A callback to automatically call when the request has finished.

## Returns:

* http\Client, self.

## Throws:

* http\Exception.

## Example:

    (new http\Client)->enqueue(new http\Client\Request("GET", "http://php.net"), 
        function(http\Client\Response $res) {
            printf("%s returned %d\n", $res->getTransferInfo("effective_url"), $res->getResponseCode());
            return true; // dequeue
    })->send();

Yields:

    http://php.net/ returned 200
