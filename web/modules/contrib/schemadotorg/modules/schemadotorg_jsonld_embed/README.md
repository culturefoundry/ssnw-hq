Table of contents
-----------------

* Introduction
* Features
* Notes


Introduction
------------

The **Schema.org Blueprints JSON-LD Embed module** extracts embedded media 
and content from an entity and includes the associated Schema.org type 
in JSON-LD.


Features
--------

- Supports embedded media via the media.module
- Support embedded entity via the entity_embed.module


Notes
-----

This module extracts embedded entities by looking for the `data-entity-type` and 
`data-entity-uuid` attributes in any `text_long` and  `text_with_summary` 
field values. 

The current user must have access to embedded entity to have the embedded entity 
included in the page's JSON-LD.
