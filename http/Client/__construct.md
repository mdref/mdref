# void http\Client::__construct([string $driver = NULL[, string $persistent_handle_id = NULL]])

Create a new HTTP client.

Currently only "curl" is supported as a $driver, and used by default.
Persisted resources identified by $persistent_handle_id will be re-used if available. 

## Params:

* string $driver = NULL  
  The HTTP client driver to employ. Currently only the default driver, "curl", is supported.
* string $persistent_handle_id = NULL  
  If supplied, created curl handles will be persisted with this identifier for later reuse.

# Throws:

* http\Exception
