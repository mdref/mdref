# static void http\Object::setDefaultErrorHandling(int $eh)

Set the http extension's global default error handling.

## Params:

* int $eh  
  The error handling. See http\Object::EH_* constants.
  
## Throws:

* http\Exception  
  When http\Object::EH_THROW is in effect.
  
## Warnings:

* E_RUNTIME, if $eh is an unknown error handling.
