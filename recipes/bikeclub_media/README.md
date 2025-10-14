# Bike Club - Media

This recipe installs configuration for media types (image and file), image styles, and related taxonomies.
 
### Contributed Modules

Module 					| Description
--------------------	|------------
Crop					| Provides storage and API for image crops.
Entity Browser			| Provides a generic entity browser/picker/selector.
Field Group				| Provides the ability to group fields on both form and display.
Image Widget Crop		| Provides an interface for using the features of the Crop API.
Magnific Popup 			| Provides Magnific Popup formatter for File Entity and Image fields.
SVG Image				| Overrides the standard image formatter and widget to support SVG files.
Views Slideshow 		| Provides a View style that displays rows as a jQuery slideshow.
Views Slideshow Cycle 	| Adds a Rotating slideshow mode to Views Slideshow.

### Image Styles and Usage

Image style 	| Aspect ratio | How it's used  
----------------|--------------|-------------------  
Banner			| 20:7	| Display one image (or a slideshow) as a page banner on the home page or above the page title on a Basic Page or Event.
Gallery			| 3:2 	| Display a gallery of images on the home page or above the page title on a Basic Page or Event.
Image, 4:3		| 4:3 	| Insert an image with aspect 4:3 in the text editor and select a size (S, M, or L).
Image, 16:9		| 16:9	| Insert an image with aspect 16:8 in the text editor and select a size (S, M, or L).

<small>Note: The ride page image style is installed with the ride content types.</small>

### Default Content

**Taxonomies**

Vocabulary	| Terms
------------|----------------------------------  
image_category | Announcement, Banner, Event, Recurring ride, Ride, Icon, People, Other
file_category  | Admin files, Member files, Public files, Cue sheet


When images are added to web pages, code in the club_media module automatically populates the image category with the name of the content type (Announcement, Banner, Event, Recurring ride, Ride).




