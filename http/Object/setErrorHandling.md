# http\Object http\Object::setErrorHandling(int $eh)

Set instance error handling.

## Params:

* int $eh  
  The error handling this instance should enforce.

## Returns:

* http/Object, self.

## Throws:

* http\Exception  
  When http\Object::EH_THROW is in effect.

## Warnings:

* E_RUNTIME, if $eh is an unknown error handling code.
