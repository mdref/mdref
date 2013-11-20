# class http\Header implements Serializable

The http\Header class provides methods to manipulate, match, negotiate and serialize HTTP headers.

## Constants:

* MATCH_LOOSE  
  None of the following match constraints applies.
* MATCH_CASE  
  Perform case sensitive matching.
* MATCH_WORD  
  Match only on word boundaries (according by CType alpha-numeric).
* MATCH_FULL  
  Match the complete string.
* MATCH_STRICT  
  Case sensitively match the full string (same as MATCH_CASE|MATCH_FULL).

## Properties:

* public $name = NULL  
  The name of the HTTP header.
* public $value = NULL  
  The value of the HTTP header.
