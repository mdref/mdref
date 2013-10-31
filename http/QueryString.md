# class http\QueryString extends http\Object implements Serializable, ArrayAccess, IteratorAggregate

The http\QueryString class provides versatile facilities to retrieve, use and manipulate query strings and form data.

## Constants:

* TYPE_BOOL  
  Cast requested value to bool.
* TYPE_INT  
  Cast requested value to int.
* TYPE_FLOAT  
  Cast requested value to float.
* TYPE_STRING  
  Cast requested value to string.
* TYPE_ARRAY  
  Cast requested value to an array.
* TYPE_OBJECT  
  Cast requested value to an object.


## Properties:

* private $instance = NULL  
  The global instance. See http\QueryString::getGlobalInstance().
* private $queryArray = NULL  
  The data array.


