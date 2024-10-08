Table of contents
-----------------

* Introduction
* Features
* Configuration
* Requirements
* References

Introduction
------------

Adds Schema.org content types to the add content type tray.


Features
--------

- Defines type categories and associated Schema.org types.
- Defines default text to be used when building links to existing content.
- Discovers and sets icons and thumbnails placed in the below directories.
  - MODULE\_NAME/images/schemadotorg\_type\_tray/icon 
  - MODULE\_NAME/images/schemadotorg\_type\_tray/thumbnail

Configuration
-------------

- Go to the Schema.org types configuration page.  
  (/admin/config/schemadotorg/settings/types#edit-schema-types)
- Enter Schema.org type categories used to organize Schema.org types throughout the admin UI/UX.

- Go to the Schema.org types configuration page.  
  (/admin/config/schemadotorg/settings/types#edit-schemadotorg-type-tray)
- Indicate the text to use when building a link to allow quick access to all nodes of a given type


Requirements
------------

**[Type Tray](https://www.drupal.org/project/type_tray)**  
Improve usability of the 'Node Add' page with more help text, functional grouping and iconography.


Notes
-----

- See [default icons](https://git.drupalcode.org/project/schemadotorg/-/tree/1.0.x/modules/schemadotorg_type_tray/images/schemadotorg_type_tray/icon?ref_type=heads)
  included in the schemadotorg_type_tray.module.
- Included icons are from the free [FontAwesome](https://fontawesome.com/) icons.

- Create free icons using [Font Awesome 2 PNG ](https://fa2png.app/)
  - Icons are 100px x 100px and #555555 hex and 85-85-85 rgb.
- Create paid icons via download and convert
  - Download icon SVG.
  - Set fill="#555555"
  - Convert SVG to PNG using https://cloudconvert.com/svg-to-png with height set 100px
  - Set canvas size to 100x100 using https://pixlr.com/editor/.


References
----------

- [Use Type Tray to improve editorial UX](https://architecture.lullabot.com/adr/20220503-use-type-tray/)
- [Improve the Editorial Experience with Type Tray and Page Templates](https://www.lullabot.com/articles/improve-editorial-experience-type-tray-and-page-templates)
