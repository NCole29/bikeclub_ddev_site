# Bike Club - CiviCRM Configuration

## Overview

This recipe installs Drupal modules and configuration to integrate CiviCRM with Drupal.

Drupal and CiviCRM must be installed first.
To install a new Drupal site with CiviCRM in your local development environment, use the scripts located in **[bikeclub/bikeclub-scripts](https://github.com/NCole29/bikeclub-scripts)**. The CiviCRM install scripts follows the [CiviCRM Installation Guide](https://docs.civicrm.org/installation/en/latest/drupal/).

## Drupal Modules Installed to Integrate CiviCRM and Drupal

Module | Description
-------|------------
CiviCRM entity		| Expose CiviCRM entities as Drupal entities.
CiviCRM group roles	| Sync Drupal Roles to CiviCRM Groups.
CiviCRM member roles| Sync CiviCRM Contacts with Membership Status to a specified Drupal Role.
CiviCRMtheme        | CiviCRM submodule to define alternate CiviCRM themes.
Webform CiviCRM		| Webform integration with CiviCRM.

## Additional Drupal Modules

Module | Description
-------|------------
CSV serialization    | Allows CSV file exports.
Honeypot	         | Mitigates spam form submissions. 
Webform views	     | Webform integration with views.	   

## Features of Installed Configuration

**CiviCRM entities**
CiviCRM information about contacts, members, and event registrations is exposed to Drupal and used in "Views" accessed from links on the People page.

**Member Roles**
The system is configured to create a Drupal USER account for club members when members when member registration is complete. Members may log in to access account information (membership expiration date, event registrations) and member-only content, if it exists.

**Group Roles**
If a CiviCRM mailing list ("group") is maintained for Ride leaders, this group may be sychronized with the Drupal "Ride leader" role. 