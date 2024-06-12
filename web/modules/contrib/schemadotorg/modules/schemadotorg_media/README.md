Table of contents
-----------------

* Introduction
* Features
* Configuration
* Notes


Introduction
------------

The **Schema.org Blueprints Media module** integrates the Media and
Media Library module with the Schema.org Blueprints module.


Features
--------

- Installs the Media Schema.org mapping type.
- Sets default sources for Schema.org
  [MediaObject](https://schema.org/MediaObject) types.
- Adds 'Media source' select element to the 'Add Schema.org media type' form.
- Sets the default entity form display component for media to the media library.


Configuration
-------------

- Go to the Schema.org types configuration page.  
  (/admin/config/schemadotorg/settings/types#edit-schemadotorg-media)
- Enter default sources for Schema.org MediaObject types.


Notes
-----

- Media style default configuration is based on the media types provides
  by Drupal's Standard installation profile.
  @see web/core/profiles/standard/config/optional
