# SympaSession
A rough and ready php class to allow a php application to authenticate a user for wwsympa

## Usage example

([Chris Hastie])

I put together to enable an existing PHP application to set Sympa's session cookie. Obviously all the authentication logic is handled by the PHP app. Once that is satisfied that the user should be authenticated to sympa it can use this class to make the appropriate updates to sympa's session_table, then set a cookie:

```php
include ('SympaSession.php');
$ss = new SympaSession($email);
$sympa_session_id = $ss->getid();
setcookie("sympa_session", $sympa_session_id, 0, '/',
$sympa_session_domain, FALSE);
```

It suits my purposes, but is not hugely flexible. See it as an example to be modified, not a fully tested and guaranteed working class.

[Chris Hastie]: mailto:lists@oak-wood.co.uk
