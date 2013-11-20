# void http\Params::__construct([mixed $params = NULL[, mixed $ps = http\Params::DEF_PARAM_SEP[, mixed $as = http\Params::DEF_ARG_SEP[, mixed $vs = http\Params::DEF_VAL_SEP[, int $flags = http\Params::PARSE_DEFAULT]]]]])

Instantiate a new HTTP (header) parameter set.

## Params:

* Optional mixed $params  
  Pre-parsed parameters or a string to be parsed.
* Optional mixed $ps  
  The parameter separator(s).
* Optional mixed $as  
  The argument separator(s).
* Optional mixed $vs  
  The value separator(s).
* Optional int $flags  
  The modus operandi. See http\Params::PARSE_* constants.

## Throws:

* http\Exception\InvalidArgumentException
* http\Exception\RuntimeException

