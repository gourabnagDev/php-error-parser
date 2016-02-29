# PHP-ERROR-LOG-PARSER
> A parser implemented on PHP. Parses PHP-Error Logs and returns them in a nice data structure.

First Clone This Github Repo:

`$ git clone https://github.com/gourabNagDev/php-error-parser && cd php-error-parser`

There would be a script named start.server.php, run that using the cli and it would tell to do open up a localhost on port 7612 as following:

```
$ php start.server.php
# Starting server
# Open the browser and point towards the following url: localhost: 7612
# To stop the server press Ctrl+C ...
```


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
