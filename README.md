###############################################
Google Map Basic
Pre 0.1 proof of concept
###############################################

Developer
-----------------------------------------------
Nicolaas Francken [at] sunnysideup.co.nz

Requirements
-----------------------------------------------
SilverStripe 3.0 or greater.

Documentation
-----------------------------------------------
1. create authentication key:http://code.google.com/apis/maps/signup.html
2. set configs
3. add decorator to sitetree + controller (see config)
4. create custom js file (if needed)
5. to include, add $GoogleMapBasic to your template...

Installation Instructions
-----------------------------------------------
1. Find out how to add modules to SS and add module as per usual.

2. copy configurations from this module's _config.php file
into mysite/_config.php file and edit settings as required.
NB. the idea is not to edit the module at all, but instead customise
it from your mysite folder, so that you can upgrade the module without redoing the settings.

3. add <% include GoogleMapBasic %> to your template

4. go into CMS and add a map to a page type that can have maps (as set in config)

5. review on screen and code CSS for the right look and feel.
