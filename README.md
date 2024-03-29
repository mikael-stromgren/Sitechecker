Sitechecker v. 1.0
Author: Mikael Strömgren

Tested with Wordpress 4.9.5 and PHP 7.1.4 (x64)

Sitechecker lets you check the status of websites. It is a single-file Wordpress plugin and is installed using the instructions for manual plugin installation 
found at the end of this READ.ME file. Also found at https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation

Installation of the plugin creates two admin screens:

Sitechecker 
- Where you can check the status of a list of websites of your choice. Four websites are added to the list at installation. 
The screen is updated automatically every 60 seconds. This check frequency can be changed to suit the user's needs.

Settings
- Where you can add and delete websites to/from the list, as well as change how frequently the check should be done. 


TODO
- Language support
- Deal with cURL statuscode == 0 at error


=========================================================================

Manual Plugin Installation
There are a few cases when manually installing a WordPress Plugin is appropriate.

If you wish to control the placement and the process of installing a WordPress Plugin.
If your server does not permit automatic installation of a WordPress Plugin.
The WordPress Plugin is not in the WordPress Plugins Directory.
Installation of a WordPress Plugin manually requires FTP familiarity and the awareness that you may put your site at risk if you install a WordPress Plugin incompatible with the current version or from an unreliable source.

Backup your site completely before proceeding.

To install a WordPress Plugin manually:

Download your WordPress Plugin to your desktop.
If downloaded as a zip archive, extract the Plugin folder to your desktop.
Read through the "readme" file thoroughly to ensure you follow the installation instructions.
With your FTP program, upload the Plugin folder to the wp-content/plugins folder in your WordPress directory online.
Go to Plugins screen and find the newly uploaded Plugin in the list.
Click Activate to activate it.
Check the Details readme file for customization and further instructions.

=========================================================================
