Table of contents
-----------------

* Introduction
* Features
* Requirements
* Configuration
* Issues

Introduction
------------

The **Schema.org Blueprints Scheduler** automatically enables scheduling for
Schema.org types as they are created.


Features
--------

- Enables scheduling for Schema.org types as they are created.
- Sets https://schema.org/datePublished (publish\_on) and
  https://schema.org/expires (unpublish\_on) for Schema.org types JSON-lD.


Requirements
------------

**[Scheduler](https://www.drupal.org/project/scheduler)**    
Gives content editors the ability to schedule nodes to be published and unpublished at specified dates and times in the future.


Configuration
-------------

- Go to the Schema.org types configuration page.  
  (/admin/config/schemadotorg/settings/types#edit-schemadotorg-scheduler)
- Go to the 'Scheduler settings' details.
- Enter the Schema.org types that support scheduling.


Issues
------

- [Issue #3317999: It is impossible to add media for node via media library if Scheduler content moderation integration module is enabled](https://www.drupal.org/project/scheduler_content_moderation_integration/issues/3317999)
