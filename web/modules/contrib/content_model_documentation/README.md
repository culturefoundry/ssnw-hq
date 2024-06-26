
Content Model Documentation's intent is to surface both the content model and
architecture of a Drupal site. It allows additional documentation of fields,
entities that use them, modules and other site related notes.

## Relevant Paths

* Help README.md [/admin/help/content_model_documentation](/admin/help/content_model_documentation)
* Permissions [/admin/people/permissions/module/content_model_documentation](/admin/people/permissions/module/content_model_documentation)
* Settings [/admin/config/system/content_model_documentation](/admin/config/system/content_model_documentation) -
  The settings to control which entity types are documentable.  Fields can be
  added to Content Model Documents as well.
* Content Model Documents [/admin/structure/cm_document](/admin/structure/cm_document)
* Content Model Reports [/admin/reports/content-model/](/admin/reports/content-model/)
* System Reports [/admin/reports/system](/admin/reports/system)

## Features

  * System Reports
    * Enabled Modules list [/admin/reports/system/enabled-modules](/admin/reports/system/enabled-modules) -
    A list of all enabled modules with links to the project page, the help page
    and a link to existing Content Model Documents for the module.
    * Workflow States and Transitions Diagrams [/admin/reports/system/workflow](/admin/reports/system/workflow)
      Diagrams show how Workflow States relate to each other by Transition.
  * Content Model Reports
    * Node count report [/admin/reports/content-model/node-count](/admin/reports/content-model/node-count) -
      A listing of all node content types and counts for how many exist.
    * Vocabulary count report [/admin/reports/content-model/vocabulary-count](/admin/reports/content-model/vocabulary-count) -
      A listing of all vocabulary types and counts for how many exist.
    * Field search [/admin/reports/content-model/field-search](/admin/reports/content-model/field-search) -
    Provide an interface to search through all active fields of your site.
    * Content Model Fields View [/admin/reports/content-model/fields/](/admin/reports/content-model/fields/) -
    A list of all fields used in the Drupal instance.  Displaying labels,
    description, entity types, bundles, and other information.  This View, once
    the module is installed, can be edited with Views UI to make add or remove
    columns or filters.
    * Entity Relationship Diagrams [/admin/reports/content-model/entity-diagram](/admin/reports/content-model/entity-diagram/) -
    Diagrams that show references from one entity to another.


  * Content Model Documents list [/admin/structure/cm_document](/admin/structure/cm_document) -
    The list of Content Model Documents.  The list is a View that can be
    modified.
  * Export and Import Content Model Documents so that documentation can ride with
    code changes.

## Export and Import Content Model Documents

### Exporting
  Exporting content model documents can be done using Drush to export them as yml files.
  The key to exports is the entity id.
  1. Visit the config page for the module and set the machine name for the local
    custom module that you want to contain the yml files for any exported CM Documents.
  2. Create or edit a CM Document.  Save your work
  3. Hover over the "edit" tab to reveal the id of the CM Document.
  4. In your terminal type `drush content-model-documentation:export <id>`
     A yml file was just created in your local module. at `<local_module>/cm_documents/<page-alias>.yml`
  5. Commit this export file like any other. (repeat steps as needed)

### Importing
  The key to importing is the alias of the exported page.

#### Import using hook_update_n()

  1. In your local module (usually the same one that is used for saving the exports)
    add or open the .install file.
  2. Gather the aliases from the pages you want to import (not including the domain).
  3. Add the something similar to this to the .install file.

  ```php
    use Drupal\content_model_documentation\CmDocumentMover\CmDocumentImport;

    /**
     * Import some CM Documents.
    */
    function <local_module>_update_9017() {
      $cm_documents_to_import = [
        '/admin/structure/types/manage/promo_banner/document',
        '/admin/structure/types/manage/full_width_banner_alert/document',
      ];
      $strict = TRUE;
      return CmDocumentImport::import($cm_documents_to_import, $strict);
    }
  ```
  The `$strict` determines whether the update is considered a failure if any of the
  CM Documents do not import.  TRUE will cause the hook_update to consider a failure if any of the imports get rejected.  FALSE will allow the rejections to happen but still
  consider the update a success.

  4. WHen drush updb is run, the content will create new entities or update existing.

  What causes an import to get rejected?

  * Timing.  If you export a CM Document on Monday, a coworker edits the same
    CM Document on Tuesday, and then you try to import your changed on Friday, they will get rejected so that your changes do not overwrite the changes made on Tuesday.


#### Import using Drush

  If you did not want to use a hook_update_n() to deploy your changes, you can use Drush.

  1. Find the alias of the CM Document that you want to import.
  2. Use the alias in the command `drush content-model-documentation:import '/alias/of/the/cm-document'`

## Dependencies

  * [Config Views](https://www.drupal.org/project/config_views) (config_views)
  is necessary to be able to create the Views of configuration entities.
  Caution: Config Views enables some of its own Views by default that replace
  some Drupal core list pages.
  * [Mermaid Diagram Field](https://www.drupal.org/project/mermaid_diagram_field) is used be able to add diagrams to documentation and reports.
  * Mermaid JS is required and the minified js file is referenced from a CDN.
