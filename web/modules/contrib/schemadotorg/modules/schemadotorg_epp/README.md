Table of contents
-----------------

* Introduction
* Features
* Requirements
* Configuration


Introduction
------------

The **Schema.org Blueprints Prepopulate module** configures and manages 
entity prepopulation for Schema.org relationships (i.e. entity references).


Features
--------

- Sets the node links display component's weight to -100 so that the node links appear first.
- Allows all entity reference to be prepopulated via query string parameters.
- Adds node links to prepopulate Schema.org parent/child relationships.
- Displays node links as an operations dropdown widget.

Requirements
------------

**[Entity Prepopulate](https://www.drupal.org/project/epp)**  
Prepopulate entity values via tokens.


Configuration
-------------

- Go to the Schema.org properties configuration page.  
  (/admin/config/schemadotorg/settings/properties#edit-schemadotorg-epp)
- Go to the 'Entity prepopulate settings' details.
- Enter Schema.org types with property that support entity reference prepopulation.
- Check or uncheck displaying node links as a dropdown.
