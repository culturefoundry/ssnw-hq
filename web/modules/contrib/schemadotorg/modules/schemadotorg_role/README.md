Table of contents
-----------------

* Introduction
* Features
* Configuration
* Requirements

Introduction
------------

The **Schema.org Blueprints Role** module manages roles 
(https://schema.org/role) for Schema.org properties.


Features
--------

- Allows dedicated 'Role' fields to be created for a Schema.org type.
- Adds 'Role' field values to JSON-LD property.
- Exposes 'Role' fields to JSON:API.
- Uses [Entity Reference Override](https://www.drupal.org/project/entity_reference_override)
  fields for 'Role' related fields.


Configuration
-------------

- Go to the Schema.org properties configuration page.  
  (/admin/config/schemadotorg/settings/properties#edit-schemadotorg-role)
- Go to the 'Role settings' details.
- Enter role field definitions which will be available to Schema.org properties.
- Enter Schema.org properties and their roles.
- Enter the Schema.org properties that should should use the Entity Reference 
  Override field to capture an entity references roles.


Requirements
------------

- **[Entity Reference Override](https://www.drupal.org/project/entity_reference_override)**  
  Provides entity reference field with overridable label.


Todo
----

- [Issue #2822973: Add entity_browser support to Entity Reference Override](https://www.drupal.org/project/entity_reference_override/issues/2822973)
  
