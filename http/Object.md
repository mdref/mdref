# abstract class http\Object

The http\Object class provides an error handling foundation for the rest of the http extension's framwework.

## Constants:

* EH_NORMAL  
  Normal error handling.
* EH_SUPPRESS  
  Suppress errors.
* EH_THROW  
  Throw exceptions on errors.

## Properties:

* static protected $defaultErrorHandling = NULL  
  Static default error handling.
* protected $errorHandling = NULL  
  Per instance error handling.
