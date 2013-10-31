# array http\QueryString::getArray(string $name[, mixed $defval = NULL[, bool $delete = false)

Retrieve an array value with at offset $name.

## Params:

* string $name  
  The key to look up.
* Optional mixed $defval = NULL  
  The default value to return if the offset $name does not exist.
* Optional bool $delete = false  
  Whether to remove the key and value from the querystring after retrieval.
  
## Returns:

* array, the (casted) value.
* mixed, $defval if offset $name does not exist.
