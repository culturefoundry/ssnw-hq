Table of contents
-----------------

* Introduction
* Features
* Configuration
* Requirements


Introduction
------------

The **Schema.org Blueprints Entity Reference Override** integrates the 
Entity Reference Override field to add a https://schema.org/Role 
to entity references.


Features
--------

- Uses [Entity Reference Override](https://www.drupal.org/project/entity_reference_override)
  fields for 'Role' related fields.
- Alters [Entity Reference Override](https://www.drupal.org/project/entity_reference_override)
  fields by Schema.org property.


Configuration
-------------

- Go to the Schema.org properties configuration page.  
  (/admin/config/schemadotorg/settings/properties#edit-schemadotorg-entity-reference-override)
- Go to the 'Entity Reference override settings' details.
- Enter the Schema.org properties that should use the Entity Reference 
  Override field to capture an entity references roles.
- Enter the Schema.org properties that should use the Entity Reference 
  Override field altered.


Requirements
------------

- **[Entity Reference Override](https://www.drupal.org/project/entity_reference_override)**  
  Provides entity reference field with overridable label.


Todo
----

- [Issue #2822973: Add entity_browser support to Entity Reference Override](https://www.drupal.org/project/entity_reference_override/issues/2822973)
  
