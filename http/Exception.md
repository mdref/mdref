# interface http\Exception

The http extension's Exception interface.

Use it to catch any Exception thrown by pecl/http.

The individual exception classes extend their equally named native PHP extensions, if such exist, and implement this empty interface. For example the http\Exception\BadMethodCallException extends SPL's BadMethodCallException.

## Properties:

None.

## Example:

    <?php
    $req = new http\Env\Request;
    
    try {
        $messages = $req->splitMultipartBody();
    } catch (http\Exception\BadMethodCallException $e) {
        // doh, no multipart message
    } catch (http\Exception\BadMessageException $e) {
        // failure while parsing
    } catch (http\Exception $e) {
        // here we used the interface to catch any http\Exception
    } catch (Exception $e) {
        // catch any other exception (unlikely, though)
    }
    ?>
