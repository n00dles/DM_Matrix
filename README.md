# What Is The Matrix

The Matrix is a plugin designed to allow for easy plugin development. It allows users to implement data structures similar to simple SQL matrices and tables in order to store the data required for their plugins, and to easily generate, manage and manipulate said tables.

There are other additional features intended for easier plugin development that are included but not yet complete, such as dynamic access to the main .htaccess, quick deployment of your CSS and JS and more.

NOTE: If you are updating from a previous version of The Marix, PLEASE BACKUP YOUR DATA/OTHER/MATRIX FOLDER. If any plugin-breaking problems arise, you can revert to that backup and use the previous iteration of The Matrix. 

## Changelog (1.0)
- Complete overhaul moving the plugin from procedural code to Object Oriented code. The majority of the useful front-end functions still have a global equivalence for backwards compatibility (e.g. you can still use DM_query to perform queries).
- Removed field types: datepicker
- Added field types: password (encodes in sha1), tags (used xoxco's jquery plugin like shawn_a's tags plugin, bbcodeeditor (markitup), wikieditor (markitup), markdown (markitup), dropdowncustom (custom dropdown menus), users (user list), components (component list), datetimelocal (html5 as opposed to javascript) and more
- Checkbox now lets you have multiple defined options
- Multiple field properties added, including 'class' (for layout in admin panel) and default (default value)
- XML files are accessed and parsed using Array2XML and XML2Array PHP class
- Schema array has been modified (fields array is structured to show the fields then their properties, rather than being an array of the existing properties and their field names)
- Query pagination is possible
- and more...


Need to add comprehensive documentation to the lineup, but for now manipulating the tables from within The Matrix plugin's interface should be fun to tinker with. 

# Use
- Unzip to the plugins folder and enable the plugin.
- Go to 'The Matrix' tab and start creating your tables.
- To output information from your plugin, do the following: