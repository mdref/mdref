# object http\Client::getTransferInfo(http\Client\Request $request)

Get transfer related informatioin for a running or finished request.

## Params:

* http\Client\Request $request  
  The request to probe for transfer info.

## Returns:

* object, stdClass instance holding transfer related information.

## Throws:

* http\Exception.

## Example:

The transfer info might look as follows:

    object(stdClass)#6 (36) {
      ["effective_url"]=>
      string(16) "https://php.net/"
      ["response_code"]=>
      int(302)
      ["total_time"]=>
      float(1.011938)
      ["namelookup_time"]=>
      float(0.203125)
      ["connect_time"]=>
      float(0.387202)
      ["pretransfer_time"]=>
      float(0.794423)
      ["size_upload"]=>
      float(0)
      ["size_download"]=>
      float(272)
      ["speed_download"]=>
      float(268)
      ["speed_upload"]=>
      float(0)
      ["header_size"]=>
      int(197)
      ["request_size"]=>
      int(91)
      ["ssl_verifyresult"]=>
      int(0)
      ["filetime"]=>
      int(-1)
      ["content_length_download"]=>
      float(272)
      ["content_length_upload"]=>
      float(0)
      ["starttransfer_time"]=>
      float(1.011835)
      ["content_type"]=>
      string(29) "text/html; charset=iso-8859-1"
      ["redirect_time"]=>
      float(0)
      ["redirect_count"]=>
      int(0)
      ["connect_code"]=>
      int(0)
      ["httpauth_avail"]=>
      int(0)
      ["proxyauth_avail"]=>
      int(0)
      ["os_errno"]=>
      int(0)
      ["num_connects"]=>
      int(1)
      ["ssl_engines"]=>
      array(3) {
        [0]=>
        string(4) "rsax"
        [1]=>
        string(6) "rdrand"
        [2]=>
        string(7) "dynamic"
      }
      ["cookies"]=>
      array(0) {
      }
      ["redirect_url"]=>
      string(15) "http://php.net/"
      ["primary_ip"]=>
      string(11) "72.52.91.14"
      ["appconnect_time"]=>
      float(0.794327)
      ["condition_unmet"]=>
      int(0)
      ["primary_port"]=>
      int(443)
      ["local_ip"]=>
      string(13) "192.168.1.120"
      ["local_port"]=>
      int(51507)
      ["certinfo"]=>
      array(0) {
      }
      ["error"]=>
      string(0) ""
    }
