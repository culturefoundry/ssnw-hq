Table of contents
-----------------

* Introduction
* Features
* Requirements
* Configuration
* References
* Notes


Introduction
------------

The **Schema.org Blueprints Metatag module** automatically adds a metatag field
to Schema.org types with default tags for Schema.org properties.

> Schema.org will one-day supersede meta tags. The default tags supported by
> this module is a collaborative community effort to map Schema.org properties
> to common meta tags.


Features
--------

- Removes Metatag Schema.org module from requirements.
- Automatically adds a metatag field to Schema.org types.
- Sets default metatags using tokens based on Schema.org properties. 


Requirements
------------

**[Metatag](https://www.drupal.org/project/metatag)**  
Manages meta tags for all entities.

**[Token](https://www.drupal.org/project/token)**  
Provides placeholder variables (tokens) and an interface for browsing available tokens.


Configuration
-------------

- Go to the Schema.org types configuration page.  
  (/admin/config/schemadotorg/settings/types#edit-schemadotorg-metatag)
- Go to the 'Metatag settings' details.
- Enter allowed meta tag groups to be displayed on node edit forms.

- Go to the Schema.org properties configuration page.  
  (/admin/config/schemadotorg/settings/properties#edit-schemadotorg-metatag)
- Go to the 'Metatag settings' details.
- Enter Schema.org properties which are mapped to meta tags.


References
----------

- [Schema.org And Metadata in Drupal](https://www.droptica.com/blog/schemaorg-and-metadata-drupal/)


Notes
-----

- [Issue #3108108: Allow which metatags are visible on the field widget to be editable](https://www.drupal.org/project/metatag/issues/3108108)
