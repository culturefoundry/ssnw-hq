Table of contents
-----------------

- Introduction
- Features
- Requirements
- Configuration
- Notes
- References


Introduction
------------

The **Schema.org Blueprints Pathauto*- automatically enables scheduling for
Schema.org types as they are created.


Features
--------

- Adds \[node:schemadotorg:base-path\] to pathauto's safe tokens
- Creates Pathauto patterns for Schema.org types.
- Provides custom Schema.org type (and subtype) base path token. 
  (i.e., \[node:schemadotorg:base-path\])


Requirements
------------

**[Pathauto](https://www.drupal.org/project/pathauto)*-    
Automatically generates URL/path aliases for various kinds of content (nodes, taxonomy terms, users) without requiring the user to manually specify the path alias.


Configuration
-------------

- Go to the Schema.org types configuration page.  
  (/admin/config/schemadotorg/settings/types#edit-schemadotorg-pathauto)
- Go to the 'Pathauto settings' details.
- Enter the Schema.org types that support pathauto.
- Enter the paths for Schema.org types.


Notes
-----

- Keep URLs as simple as possible
- Limit URLs to two to three path parts
  - https://www.domain.com/[audience]/[category]/[title]
  - https://www.domain.com/[category]/[title] 
- The audience and category path part should ensure that all URLs are unique.
  For example, the same page title could be used on the website, 
  but the page is placed in a different category.
- The category path part should be plural and resolve to a view page.
- Every part of the path should resolve to a page or view.


References
----------

- [Pathauto | Drupal.org](https://www.drupal.org/project/pathauto)
- [URL Structure: Best Practices for SEO-Friendly URLs › Design Powers](https://designpowers.com/blog/url-best-practices) 
