Table of contents
-----------------

* Introduction
* Features
* Configuration
* Requirements
* Notes


Introduction
------------

The **Schema.org Blueprints Paragraphs module** integrates the Paragraphs
and Paragraphs Library module with the Schema.org Blueprints module.


Features
--------

- Install the Paragraph Schema.org mapping type.
- Defines default widget setting for paragraphs.
- Allows Schema.org types mapped to a paragraph to use the Paragraphs library.
- Convert broken Paragraph embed entity reference widget into a working
  select menu.
- Add paragraph type icons from provided this and other modules. 
  {module_name}/image/schemadotorg\_paragraphs/{paragraph\_type}.svg
- Adds paragraph from paragraphs library to JSON-LD.
- Limit a paragraph's property/field access based on the paragraph's parent
  Schema.org mapping type.


Configuration
-------------

- Go to the Schema.org types configuration page.  
  (/admin/config/schemadotorg/settings/types#edit-schemadotorg-paragraphs)
- Enter Schema.org types that should automatically support being used via the Paragraphs library.
- Enter Schema.org parent type and paragraph types that have limited 
  Schema.org properties.


Requirements
------------

**[Paragraphs](https://www.drupal.org/project/paragraphs)**  
Enables the creation of paragraphs entities.


Notes
-----

- Icons are from [Font Awesome](https://fontawesome.com/)
