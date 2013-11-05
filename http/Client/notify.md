# http\Client http\Client::notify([http\Client\Request $request = NULL[, object $progress = NULL]])

Implements SplSubject. Notify attached observers about progress with $request.

## Params:

* Optional http\Client\Request $request = NULL  
  The request to notify about.
* Optional object $progress = NULL  
  stdClass instance holding progress information.

## Returns:

* http\Client, self.

## Example:

    <?php
    class Observer implements SplObserver {
        function update(SplSubject $client, http\Client\Request $request = NULL, $progress = NULL) {
            printf("%s %d%%\n", $progress->info,
                $progress->dltotal ? ($progress->dlnow*100/$progress->dltotal) : 0);
        }
    }
    
    $client = new http\Client;
    
    $client->attach(new Observer);
    $client->enqueue(new http\Client\Request("GET", "https://php.net/images/logos/php-logo.eps"));
    $client->send();
    ?>

May yield:

    start 0%
    setup 0%
    setup 0%
    setup 0%
    setup 0%
    setup 0%
    resolve 0%
    connect 0%
    connected 0%
    connected 0%
    connected 0%
    ssl negotiation 0%
    ssl negotiation 0%
    ssl negotiation 0%
    ssl negotiation 0%
    ssl negotiation 0%
    ssl negotiation 0%
    ssl negotiation 0%
    ssl negotiation 0%
    ssl negotiation 0%
    ssl negotiation 0%
    ssl negotiation 0%
    ssl negotiation 0%
    ssl negotiation 0%
    ssl negotiation 0%
    ssl negotiation 0%
    ssl negotiation 0%
    ssl negotiation 0%
    ssl negotiation 0%
    blacklist check 0%
    receive 10%
    receive 20%
    receive 30%
    receive 40%
    receive 50%
    receive 60%
    receive 70%
    receive 80%
    receive 90%
    receive 100%
    receive 100%
    not disconnected 100%
    finished 100%
