Table of contents
-----------------

* Introduction
* Features
* Notes
* Configuration
* Requirements


Introduction
------------

The **Schema.org BlueprintsContent Model Documentation module** integrates 
the Content Model Documentation module with the Schema.org Blueprints module.


Features
--------

Schema.org mapping

- Creates content model documentation for the Schema.org mappings.
- Links to content model documentation on node add/edit forms 
  using the help block on node add/edit forms or a markup field.
- Opens node add/edit form content model documentation link in a modal.
- Adds 'Relationships' operation to Schema.org mappings.

Content model documentation

- Allows Schema.org mapping documentation to be created on production before
  a Schema.org mapping and entity is pushed to production. 
- Sets default template and format for Notes.
- Adds 'Open in new tab' button to modal dialogs.
- Moves 'Fields that appear on ...' table into a details widget.
- Adds access control to viewing 'Fields that appear on ...'.
- Overrides the Content Model Document view builder to allow schema_* fields to 
  appear as expected.


Notes
-----

Installing the Markup module is strongly recommended because using a Markup 
field for the summary and link ensures it will always appear in different 
contexts, including modal dialogs.



Configuration
-------------

- Go to the Schema.org properties configuration page.  
  (/admin/config/schemadotorg/settings/properties#schemadotorg_content_model_documentation)
- Go to the 'Content model documentation settings' details.
- Enter the entity types that should automatically generate corresponding content model documentation.
- Enter default HTMl template to be used new documentation.
- Select the default format used by the documentation notes field.
- Enter the text to be displayed when linking to a node's content model documentation.
- Enable/disable opening node's content model documentation link in a a modal.
- Update documentation to all existing Schema.org mappings.


Requirements
------------

**[Content Model Documentation](https://www.drupal.org/project/content_model_documentation)**  
Adds admin displays for the site architecture and history.

**[Markup](https://www.drupal.org/project/markup)** OPTIONAL  
Defines a field type for markup.


