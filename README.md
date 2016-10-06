# dynamics-php
Dynamics CRM PHP application which uses only uses raw SOAP based requests to authenticate and work with CRM Online and On Premise IFD environments.

# Endpoint: authenticate.php
Expected request.POST parameters of instance_url, username, and password. If failure, returns empty json object. If success, returns userid like so: {"userid": "6c3098e1-103e-e611-80dc-00155d021a24"}

Note that this endpoint automatically escapes the password for you. Any new endpoints created must be sure to escape the password, like so:

```
$password = str_replace('"', "&quot;", $password);
$password = str_replace("'", "&apos;", $password);
$password = str_replace('<', "&lt;", $password);
$password = str_replace('>', "&gt;", $password);
$password = str_replace("&", "&amp;", $password);
```
