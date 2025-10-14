# Bike Club - Location Content Type

This recipe installs:

- Location content type with fields, form, and display.
- Custom administrative view of locations on the admin Content page.
- View of "Ride starts" in map and list formats.
- Block display of the "Ride starts" map for placement on the home page.
- Location taxonomy to categorize locations.

Once created, locations may be selected in Event, Ride, and Recurring ride content types.

### Contributed Modules Included in the Recipe

Module 				  | Description
----------------------|------------
Address				  | Provides functionality for storing, validating and displaying postal addresses.
Field Group			  | Provides for groups of fields to be organized on forms.
Geocoder Field 		  | Geocodes the content of a field and stores the result in other field and vice-versa.
Geofield 			  | Stores geographic and location data (points, lines, and polygons).
Leaflet				  | Provides integration with the Leaflet JS mapping library.
Leaflet Views 		  | Views integration for the Leaflet module.
Node Revision Delete  | Lets you set how many revisions to keep and for how long.

### Taxonomy for Type of Location

When creating a location, the field for **Type of Location** is a select list using the **location taxonomy**.
The following location taxonomy terms may be added by applying the bikeclub-default_content recipe. A subset of these, or alternatives, may be added manually.

- Eating place
- Event place
- Meeting place
- Remote ride start
- Ride start
- Trailhead

The above terms may be used to filter locations on the Content > Locations admin page.

In addition, the **Club location** view includes displays of **ride starts** in list and map format.
 - If Location type is **Ride start**, the location is displayed on the ride start list and map.
 - If location type is **Remote ride start**, the location is displayed on the ride start list but not on the map. Use this term for places outside the desired map window.
