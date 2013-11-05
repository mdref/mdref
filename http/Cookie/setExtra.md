# http\Cookie http\Cookie::setExtra(string $extra_name[, string $extra_value = NULL])

(Re)set an extra attribute.
See http\Cookie::addExtra().

> **Note:** The attribute will be removed from the extras list if $extra_value is NULL.

## Params:

* string $extra_name  
  The key of the extra attribute.
* Optional string $extra_value  
  The value of the extra attribute.

## Returns:

* http\Cookie, self.
