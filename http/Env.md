# class http\Env extends http\Object

The http\Env class provides static methods to manipulate and inspect the server's current request's HTTP environment.

## Request startup

The http\Env module extends PHP's builtin POST data parser to be run also if
the request method is not POST. Additionally it will handle 
application/json payloads if ext/json is available. Successfully 
parsed JSON will be put right into the $_POST array.

