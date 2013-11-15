# class http\Client implements SplSubject, Countable

The HTTP client.

## Properties:

* private $observers = NULL  
  SplObjectStorage, attached observers.
* protected $options = NULL  
  Array of set options.
* protected $history = NULL  
  http\Message request/response history.
* public $recordHistory = false  
  Boolean flag whether to record history in http\Client::$history.
