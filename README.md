# dynamics-php
Dynamics CRM PHP application which uses only uses raw SOAP based requests to authenticate and work with CRM Online and On Premise IFD environments.

# Note - must escape the password before sending it in
```
password = password.replace('"', "&quot;")
password = password.replace("'", "&apos;")
password = password.replace('<', "&lt;")
password = password.replace('>', "&gt;")
password = password.replace('&', "&amp;")
```
