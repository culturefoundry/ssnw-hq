Table of contents
-----------------

* Introduction
* Features
* Configuration


Introduction
------------

The **Schema.org Blueprints Additional Mappings** module allows 
additional Schema.org mappings to applied to a Schema.org mapping.

**Use case**

Conceptually, any page (i.e., in Drupal, a node) with a URI is a WebPage that 
can include A breadcrumb, primaryImageOfPage, relatedLink, significantLink, 
and more…

This module allows nodes to have primary Schema.org mapping to a specific 
Schema.org type while still allowing a node to be mapped to 
a Schema.org WebPage.

For example, a Recipe may also want to include a WebPages' relatedLink property 
and define a primaryImageOfPage.


Features
--------

- Enables the https://schema.org/WebPage mapping for most Schema.org nodes.
- Combine additional mappings into the main entity's JSON-LD. (i.e., `{'@type': ['MedicalStudy', 'ResearchProject']}`)


Notes
-----

- Because additional mappings are merged into the main entity's JSON-LD, 
  an additional mapping needs to only contain Schema.org properties that are 
  not included in the main entity's mapping.


Configuration
-------------

- Go to the Schema.org properties configuration page.  
  (/admin/config/schemadotorg/settings/types#edit-schemadotorg-additional-mappings)
- Go to the 'Additional mappings settings' details.
- Enter the additional Schema.org mappings for Schema.org mapping type.
- Enter default properties for Schema.org types.
