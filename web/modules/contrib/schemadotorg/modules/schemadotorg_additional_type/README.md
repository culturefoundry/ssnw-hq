Table of contents
-----------------

* Introduction
* Features
* Configuration
* References


Introduction
------------

The **Schema.org Blueprints Subtype module** enhances the Schema.org additional
type property to allow additional type specificity without having to create 
dedicated entity types for every appropriate Schema.org type.

For example, the sub types for <http://schema.org/Event> are mainly for adding a 
little extra specificity about an Event. Most event additional types do not need to 
have dedicated content types created.


Features
--------

- Adds 'Enable Schema.org additional type' to Schema.org mapping UI.
- Site builders can alter additional type field names, labels, descriptions, 
  and allowed values.
- Alters the Schema.org mapping list builder and adds a 'Additional type' column.
- Replaces @type in JSON-LD for valid additional types or uses the additional type value
  as the https://schema.org/additionalType.


Configuration
-------------

- Go to the Schema.org types settings page
  (/admin/config/schemadotorg/settings/types#edit-schemadotorg-subtype)
- Go to the 'Schema.org additional type' details.
- Enter Schema.org types that support additional typing by default.
- Enter default additional type allowed values for Schema.org types.


References
----------

- [How to use additionalType and sameAs to link to Wikipedia](https://support.schemaapp.com/support/solutions/articles/33000277321-how-to-use-additionaltype-and-sameas-to-link-to-wikipedia)
