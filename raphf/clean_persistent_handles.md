# void raphf\clean_persistent_handles([string name = NULL[, string $ident]])

Clean persistent handles whith id $name->$ident.

## Params

* Optional string $name = NULL  
  The persistent handle id.
* Optional string $ident = NULL  
  The unique identifier within the persistent handle id.

## Example:

    raphf\clean_persistent_handles("http\\Client\\Curl\\Request", "php.net:80");
