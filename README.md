# PHP-ERROR-LOG-PARSER
> A parser implemented on PHP. Parses PHP-Error Logs and returns them in a nice data structure.

Just a simple script to get you started:
```
<?php
/**
 * File Name: test_error_parser.php
 * Project: Error Parser
 */

require "src/Parser.php";

$parser = new error_parser("C:/xampp/php/logs/php_error_log");
echo $parser->returnXml();
```
