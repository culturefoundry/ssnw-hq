Table of contents
-----------------

* Introduction
* Features
* Configuration
* Notes


Introduction
------------

The **Schema.org Blueprints Custom Field module** allows a Custom field to be used to
create Schema.org relationships within an entity type/bundle Schema.org mapping.


Features
--------

- Alter the Schema.org properties configuration form to allow site builders
  to determine which properties should be mapped to a custom_field instead of
  Schema.org type.
- Appends units field suffix to custom_field widget edit forms and
  custom_field view displays.
- Appends units to custom_field JSON-LD data.


Requirements
------------

**[Custom Field](https://www.drupal.org/project/custom_field)**  
Defines a new "Custom Field" field type that lets you create simple inline multiple-value fields without having to use entity references.


Configuration
-------------

- Go to the Schema.org properties configuration page.  
  (/admin/config/schemadotorg/settings/properties#edit-schemadotorg-custom-field)
- Go to the 'Custom field settings' details.
- Set Schema.org properties that should use custom_fields and define the
  custom_field item data types.


Notes
-----

The **Schema.org Blueprints Custom Field module** is intended as proof of concept of
alternate way to manage relationships between Schema.org types.

This module may be removed before the first beta release and moved to
a sandbox module.
