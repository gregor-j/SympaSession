# SympaSession

[![Maintainability](https://api.codeclimate.com/v1/badges/f922d8f50980c01f955d/maintainability)](https://codeclimate.com/github/gregor-j/SympaSession/maintainability)

A PHP class to authenticate a user for _wwsympa_.

## Usage example

```php
include_once ('vendor/autoload.php');
/**
 * Implement your own database interface depending on your framework.
 * @var \GregorJ\SympaSession\Interfaces\DatabaseInterface $database
 */
$database;
\GregorJ\SympaSession\SympaSession::setDatabase($database);
\GregorJ\SympaSession\SympaSession::setRobot(
    new SympaSession\Entities\SympaRobot('lists.example.com/sympa')
);
/**
 * Read the email address of the user from your framework.
 */
$emailAddress = new \GregorJ\SympaSession\DataTypes\EmailAddress('example@example.com');
try {
    new \GregorJ\SympaSession\SympaSession($emailAddress);
} catch (\GregorJ\SympaSession\Exceptions\RuntimeException $exception) {
    trigger_error($exception->getMessage(), E_USER_WARNING);
} catch (\GregorJ\SympaSession\Exceptions\LogicException $exception) {
    trigger_error($exception->getMessage(), E_USER_ERROR)
}
```
