Table of contents
-----------------

* Introduction
* Features
* Configuration
* Notes


Introduction
------------

The **Schema.org Blueprints JSON-LD module** builds and adds Schema.org structured 
data as JSON-LD in the head of web pages.


Features
--------

- Adds 'application/ld+json' to the <head> section of HTML pages.
- Converts the Address field values to https://schema.org/PostalAddress
- Applies image styles to image files shared via JSON-LD.
- Converts Drupal entities to Schema.org types for JSON-LD.
- Allows entity references to be display by label, url, or entity via JSON-LD.
- Provides hooks for modules to define and alter JSON-LD for routes, 
  types, and properties.


Configuration
-------------

- Go to the Schema.org JSON:LD configuration page.  
  (/admin/config/schemadotorg/settings/jsonld#edit-schemadotorg-jsonld)
- Go to the 'JSON-LD settings' details.
- Enter the default Schema.org property order.
- Enter the Schema.org property image styles.
- Enter how an entity reference's Schema.org type should be displayed via JSON-LD.


Notes
-----

- The output from this module will escape all forwards slashes, e.g. URL
  values will output as `https://www.example.com/my/page` instead of
  `https://www.example.com/my/page`. This is by design to work around
  [limitations of JSON data in JavaScript](https://stackoverflow.com/a/1580682).

- JSON-LD is cached for anonymous and authenticated users even if a page 
  is not cached. The generation of JSON-LD requires a lot database queries 
  to look up entities and Schema.org types and properties. 
  Caching the JSON-LD is a huge performance improvement.
  - @see \schemadotorg_jsonld_page_attachments_alter()
  - @see \Drupal\schemadotorg_jsonld_preview\Plugin\Block\SchemaDotOrgJsonLdPreviewBlock::build
  - @see \Drupal\schemadotorg_jsonld_endpoint\Controller\SchemaDotOrgJsonLdEndpointController::getEntity
