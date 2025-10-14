# Drupal for Bike Clubs

Drupal-for-Bikeclubs is an open source product built with Drupal and CiviCRM to provide key components for cycling clubs:

- Membership and Event registration
- Ride calendar
- Ride pages with RideWithGPS maps and information

## Getting started

Use [DDEV](https://ddev.com) to install and setup your site in a local development environment. After completing site configuartion locally, you can easily migrate the site to a web host.

To use [DDEV](https://ddev.com), follow these instructions:

1. Install DDEV following the [documentation](https://ddev.com/get-started/)
2. Download drupal-bikeclub.zip 
3. Open a Terminal window and create a folder (e.g., my-site) where you would like to install Drupal-for-Bikeclubs. 
    ```shell
    mkdir my-site && cd my-site
    ```
4. Run the following command to configure the folder for Drupal and start ddev:
    ```shell
    ddev config --project-type=drupal11 --docroot=web
    ddev start
    ```

5. Extract the content of drupal-bikeclub.zip. 
    - Copy the composer.json and readme.md to the "my-site" folder.
    - Copy the web/profiles folder to "mysite/web/profiles".

6. Install Drupal-for-Bikeclubs with these commands:
    ```shell
    ddev composer install
    ddev launch
    ```

### Installation

The Bikeclub installer provides administrative tools and features preconfigured with smart defaults, plus four content types:

- Announcement
- Page
- Ride
- Recurring ride

After the initial install, go to **Extend** on the Administrative menu and click Recipes on the top menu to install additional recipes:

Recipe               		  | Description
--------------------------| -------------------- 
Bikeclub CiviCRM			    | CiviCRM integrates with Drupal to manage membership and event registration; it must be installed after Drupal installation is complete.
Bikeclub Dev         		  | Optional development tools for use in a local development environment.
Bikeclub Events				    | Event content type. Includes the option to obtain registration with a webform linked to CiviCRM, a CiviCRM form, or an external registration site.
Bikeclub FAQS				      | FAQs content type. Presents information in a Q-and-A format.
Bikeclub Help             | Provides custom Help pages with information specific to this installation.
Bikeclub Member directory	| Opt-in member directory to display name, city/town, and state. A link on name opens a Drupal contact form which will forward email to members without disclosing their email address on the website.
Bikeclub Paragraph types	| Three simple paragraph types which can be used to construct page layouts. Alternate sources of paragraph types may be installed instead of this recipe.
Bikeclub Webform nodes		| Webform node content type. For webforms that are used repeatedly (e.g., annual survey or volunteer signup), presenting the webform in a new webform node keeps registrations for each instance separate.


## Documentation

In addition, learn more about managing a Drupal-based application in the [Drupal User Guide](https://www.drupal.org/docs/user_guide/en/index.html).

## Attributions

The Drupal-for-Bikeclubs installer is based on Drupal CMS and employs the Drupal Recipe Installer Kit.

[Report issues in the queue](https://drupal.org/node/add/project-issue/drupal_cms), providing as much detail as you can. You can also join the #drupal-cms-support channel in the [Drupal Slack community](https://www.drupal.org/slack).

## License

Drupal-for-Bikeclubs and all derivative works are licensed under the [GNU General Public License, version 2 or later](http://www.gnu.org/licenses/old-licenses/gpl-2.0.html).

Learn about the [Drupal trademark and logo policy here](https://www.drupal.com/trademark).
