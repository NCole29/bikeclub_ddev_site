# Bike Club - Registration field storage

Installs field_storage for six fields used to implement optional registration on the **Event** and **Basic page**  content types.

Field 	| Description
----------------------------| --------------
Registration				| Select registration type to conditionally display other fields based on type.
Registration free form		| Select the free event webform (free forms display as a popup)
Registration form			| Select a paid event webform (paid forms display on a new web page)
Registration link			| Fill a URL to a CiviCRM or external registration page.
Registration close date		| Set a registration close date.
Registration results access	| Provide access for select club members to view registrations.

## Contributed Modules Required by the Recipe

Module 				  	| Description
----------------------	|------------
Honeypot				| Uses both the honeypot and timestamp methods of deterring spam bots from completing forms on your Drupal site.
Webform (and some submodules) | Comprehensive form-builder.
Webform Civicrm | Allows you to use Webform for CiviCRM membership and event registration. 
