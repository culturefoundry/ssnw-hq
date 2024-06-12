Table of contents
-----------------

* Introduction
* Features
* Notes
* Requirements
* Configuration
* FAQ


Introduction
------------

The **Schema.org Blueprints JSON:API module** builds on top of the JSON:API
and JSON:API extras modules to apply Schema.org type and property mappings
to JSON:API resources.


Features
--------

- Automatically create JSON:API endpoints for Schema.org type mappings.
- Automatically enable Schema.org properties for JSON:API endpoints.
- Automatically rename JSON:API entity and field names to use corresponding
  Schema.org types and properties.
- Adds a JSON:API column with links to the Schema.org mappings admin page.  
  (/admin/config/schemadotorg)


Notes
-----

- By default all JSON:API endpoints be disabled and only required and relevant
  endpoint and properties are enabled.  
- Schema.org properties are always exposed with some Drupal internal properties.
- Schema.org field prefixes (schema_*) should be removed.


Requirements
------------

**[JSON:API Extras](https://www.drupal.org/project/jsonapi_extras)**    
Provides a means to override and provide limited configurations to the default
zero-configuration implementation provided by the JSON:API in Core.


Configuration
-------------

- Go to the Schema.org JSON:API configuration page.  
  (/admin/config/schemadotorg/settings/jsonapi#edit-schemadotorg-jsonapi)
- Go to the 'JSON:API settings' details.
- Enter base fields that should default be enabled.
- Check/uncheck use Schema.org types as the JSON:API resource's type
  and path names.
- Check/uncheck use Schema.org properties as the JSON:API resource's field
  names/aliases.

