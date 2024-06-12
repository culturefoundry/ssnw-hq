Table of contents
-----------------

* Introduction
* Features
* Requirements
* Configuration


Introduction
------------

The **Schema.org Blueprints Field Group module** creates field groups in entity view
and form displays when Schema.org properties are mapping to a field.


Features
--------

- Adds field groups to node and user Schema.org mapping.
- Generates a generic field group based on a Schema.org mapping's type.
- Deletes empty field groups created when a field is deleted.
- Adds a field group around all displayed paragraphs.
- Allows field groups to be disabled for selected entity type, display, 
  Schema.org types, and properties. 


Requirements
------------

**[Field Group](https://www.drupal.org/project/field_group)**    
Provides the ability to group your fields on both form and display.


Configuration
-------------

- Go to the Schema.org properties configuration page.  
  (/admin/config/schemadotorg/settings/properties#edit-schemadotorg-field-group)
- Go to the 'Field group settings' details.
- Enter the default field groups and field order used to group Schema.org
  properties as they are added to entity types.
- Enter the field group label suffix used when creating new field groups.
- Select the default field group type used when adding a field group to
  an entity type's default form.
- Select the default field group type used when adding a field group to
  an entity type's default view display.
- Enter the Schema.org types and properties that should NOT have field groups.
