# class http\Params extends http\Object implements ArrayAccess

Parse, interpret and compose HTTP (header) parameters.

## Constants:

* DEF_PARAM_SEP  
  The default parameter separator (",").
* DEF_ARG_SEP  
  The default argument separator (";").
* DEF_VAL_SEP  
  The default value separator ("=").
* COOKIE_PARAM_SEP  
  TBD
* PARSE_RAW  
  Do not interpret the parsed parameters.
* PARSE_DEFAULT  
  Interpret input as default formatted parameters.
* PARSE_URLENCODED  
  Urldecode single units of parameters, arguments and values.
* PARSE_DIMENSION  
  Parse sub dimensions indicated by square brackets.
* PARSE_QUERY  
  Parse URL querystring (same as http\Params::PARSE_URLENCODED|http\Params::PARSE_DIMENSION).

## Properties:

* public $params = NULL  
  The (parsed) parameters.
* public $param_sep = http\Params::DEF_PARAM_SEP  
  The parameter separator(s).
* public $arg_sep = http\Params::DEF_ARG_SEP  
  The argument separator(s).
* public $val_sep = http\Params::DEF_VAL_SEP  
  The value separator(s).
* public $flags = http\Params::PARSE_DEFAULT  
  The modus operandi of the parser. See http\Params::PARSE_* constants.
