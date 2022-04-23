# Config Parser

Config Parser is a small CLI application created with PHP and the Symfony Framework that is able to parse yaml and json files providing the ability to traverse the cumulative merge of multiple files.

## Installation

* Clone the repository
* Please make sure you are using PHP 7.4
* Please also install composer on your system (https://getcomposer.org/)
* Perform ```composer install```
* It is recommended to run in the project root directory ```bin/console cache:clear```

## Usage

```php
//Run unit tests:
bin/phpunit


//Run command:
bin/console app:parser-files $file1 $file2 $tranversalIndex
e.g. 
bin/console app:parser-files tests/fixtures/config.json tests/fixtures/config.local.json database

//Run docker environment
docker-compose up -d

//Enter container
docker exec -it php bash //(you will be able to run the command and unit tests from the container root directory as well
```

## Notes
* ConfigParser class can handle more than two files however, the CLI command is limited to two files
* Command is available in ```src/Command```
* Config Parsing logic is available in ```src/Services```
* Unit tests and fixtures are available in ```tests``` directory
* Specification drawn from: https://github.com/dividohq/config-chg
* When running CLI command please note that the directory that the input command expects is relative to the projects directory