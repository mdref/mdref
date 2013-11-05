# string http\Client\Request::getContentType()

Extract the currently set "Content-Type" header.
See http\Client\Request::setContentType().

## Params:

None.

## Returns:

* string, the currently set content type.
* NULL, if no "Content-Type" header is set.

## Example:

    <?php
    $multi = new http\Message\Body;
    $multi->addPart(new http\Message("Content-type: text/plain\n\nHello part 1!"));
    $multi->addPart(new http\Message("Content-type: text/plain\n\nHello part 2!"));
    $request = new http\Client\Request("POST", "http://localhost/", [], $multi);
    var_dump($request->getContentType());
    ?>

Yields:

    string(49) "multipart/form-data; boundary="30718774.3fcf95cc""
