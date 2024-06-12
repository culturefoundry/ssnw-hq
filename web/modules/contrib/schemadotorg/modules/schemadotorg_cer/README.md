Table of contents
-----------------

* Introduction
* Features
* Requirements
* Configuration
* Issues


Introduction
------------

The **Schema.org Blueprints Corresponding Entity References module** improves 
https://inverseOf support using the Corresponding Entity References module.


Features
--------

- Set the default mapping property type to 'entity_reference' for 
  corresponding entity references
- Automatically creates corresponding entity references for selected 
  Schema.org properties.
- Synchronizes entity references target bundles for all Schema.org mappings.
- Alters the CorrespondingReferenceForm to exposes 'schema_*' fields to 
  corresponding entity reference entities.  
  @see  [Issue #2998138 Could support Remove field name prefix module?](https://www.drupal.org/project/cer/issues/2998138)


Requirements
------------

**[Corresponding Entity References](https://www.drupal.org/project/cer)**        
Allows users to create two-way references between entities.


Configuration
-------------

- Go to the Schema.org properties configuration page.  
  (/admin/config/schemadotorg/settings/properties#edit-schemadotorg-cer)
- Go to the 'Corresponding entity references settings' details.
- Enter default Schema.org property inverse of relationships.


Issues
------

- [Issue #2998138 Could support Remove field name prefix module?](https://www.drupal.org/project/cer/issues/2998138)
