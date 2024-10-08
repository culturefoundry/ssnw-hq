Table of contents
-----------------

* Introduction
* Features
* Requirements
* Configuration


Introduction
------------

The **Schema.org Blueprints Content Moderation** automatically enables
content moderation for Schema.org types as they are created.


Features
--------

- Do not display the 'Moderation control' view component if 
  the Moderation Sidebar module is enabled.
- Enables content moderation workflows for Schema.org (content) types as
  they are created.


Requirements
------------

**[Content moderation](https://www.drupal.org/docs/8/core/modules/content-moderation/overview)**    
Allows you to expand on Drupal's "unpublished" and "published" states for content.


Configuration
-------------

- Go to the Schema.org types configuration page.  
  (/admin/config/schemadotorg/settings/types#edit-schemadotorg-content-moderation)
- Go to the 'Content moderation settings' details.
- Enter the default content moderation workflow per entity type and Schema.org type.


Known Issues
------------

- [Issue #3468716: Default Drupal core's moderation control widget to hidden when this module is enabled](https://www.drupal.org/project/moderation_sidebar/issues/3468716)
