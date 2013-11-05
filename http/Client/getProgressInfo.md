# object http\Client::getProgressInfo(http\Client\Request $request)

Retrieve the progress information for $request.

## Params:

* http\Client\Request $request  
  The request to retrieve the current progress information for.

## Returns:

* object, stdClass instance holding progress information.
* NULL, if $request is not enqueued.

## Throws:

* http\Exception.

## Example:

The progress info may look like follows:

    object(stdClass)#6 (7) {
      ["started"]=>
      bool(true)
      ["finished"]=>
      bool(true)
      ["info"]=>
      string(8) "finished"
      ["dltotal"]=>
      float(0)
      ["dlnow"]=>
      float(33561)
      ["ultotal"]=>
      float(0)
      ["ulnow"]=>
      float(0)
    }
