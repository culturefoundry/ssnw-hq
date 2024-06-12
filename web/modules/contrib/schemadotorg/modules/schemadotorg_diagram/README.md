Table of contents
-----------------

* Introduction
* Features
* Configuration
* Requirements
* References


Introduction
------------

The **Schema.org Blueprints Diagram** module provides
diagrams for Schema.org relationships.


Features
--------

- Provides a block for displaying diagrams for the
  current route matches entity.
- Provides a node tab/task for displaying diagrams.


Configuration
-------------

Permissions

- Configure 'View Schema.org Diagram' permission.  
  (/admin/people/permissions/module/schemadotorg_diagram)

Settings

- Go to the Schema.org General configuration page.  
  (/admin/config/schemadotorg/settings/general#edit-schemadotorg-diagrams)
- Go to the 'Schema.org Diagrams settings' details.
- Check/uncheck display diagrams as a node tab/task.
- Enter Schema.org diagram title, parent, and child Schema.org properties.

Block

- Go to the Block layout page page.  
  (/admin/structure/block)
- Select, configure, and place the 'Schema.org Blueprints Diagrams' block
  on the page.


Requirements
------------

### Contribute modules (Optional)

- **[Content Model Documentation](https://www.drupal.org/project/content_model_documentation)**  
  Adds admin displays for the site architecture and history.

### JavaScript Libraries

- **[Mermaid](https://mermaid.js.org)**  
  Mermaid lets you create diagrams and visualizations using text and code.


References
----------

- https://en.wikipedia.org/wiki/Hierarchical_organization
- [SVG Export Chrome Extension](https://chrome.google.com/webstore/detail/svg-export/naeaaedieihlkmdajjefioajbbdbdjgp/related?hl=en-GB)  
  Use for exporting and saving SVG mermaidJS diagrams. 
