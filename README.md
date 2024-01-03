# hibp-checker

### Overview
Check a list of email addresses against HaveIBeenPwned service by uploading a CSV file containing all email addresses.

### Prerequisites
- [Apache](https://www.apache.org/)
- [PHP](https://www.php.net/)
- [HIBP API Key](https://haveibeenpwned.com/API/Key)

### Installation
Set up a default web server, for example Apache. Copy all files inside ```/var/www/``` folder.

### Features
###### v2:
- Increased API limit to 1 request every 6 seconds (due to change in API rate limit).


###### v1:
- Checks a list of email addresses in 2 seconds interval (not to exceed the API rate limit).
- Displays the result immediately without waiting for the script to finish.
- Saves previous results until the 'Delete' button is selected.

### Contributors
 - Geoffrey Spiteri - [geoffrey-spiteri](https://github.com/geoffrey-spiteri)
