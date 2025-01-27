Schema.org Blueprints Architecture Decisions Records (ADRs)
-----------------------------------------------------------

The below document records the "technical" functional and 
non-function decisions behind the Schema.org Blueprints module.


# 1000 - Coding standards

##### Use minimal ADRs to track architecture decisions.
- Some documentation is better than no documentation
- Self-explanatory decisions can be explained via the use case and solution

##### Follow [Drupal.org's coding standards](https://www.drupal.org/docs/develop/standards)
- Provide interfaces for all services
- Maintain README.md files for all sub-modules

##### Write basic tests for all functionality
- Some test coverage is better than no test coverage
- Public methods for services/interfaces should have test coverage

##### Write full tests for regressions

##### Use kernel tests for verifying generated content models and JSON-LD
- @see \Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgKernelEntityTestBase

##### Organize and name fields according to their use case
- Structured data (Schema.org) fields are prefixed with schema_*
- General (custom) fields are prefixed with field_*

##### Use config snapshot test to confirm an expected configuration for starter kits
- @see \Drupal\Tests\schemadotorg\Functional\SchemaDotOrgConfigSnapshotTestBase

##### Form elements should include a title and description that states the element's intent and usage

##### All hooks and APIS should be documented.
- [schemadotorg/schemadotorg.api.php](https://git.drupalcode.org/project/schemadotorg/-/blob/1.0.x/schemadotorg.api.php)
- [schemadotorg/modules/schemadotorg_jsonld/schemadotorg_jsonld.api.php](https://git.drupalcode.org/project/schemadotorg/-/blob/1.0.x/modules/schemadotorg_jsonld/schemadotorg_jsonld.api.php)

##### Use [CommonMark](https://commonmark.thephpleague.com/) with [GitHub-Flavored Markdown](https://github.github.com/gfm/).


# 2000 - Code architecture

##### Namespace everything with schemadotorg_- or SchemaDotOrg- prefix
- Sub-modules should be prefixed with schemadotorg_{module_name}
- Ensures all Schema.org code is searchable and identifiable. 

##### Use loosely coupled sub-modules that do one thing over monolithic modules doing many things

##### Use sub-modules for integration with other modules and sub-systems

##### Use sub-modules to support distinct Schema.org features
- Features include https://schema.org/Role, https://schema.org/identifier, and sub-typing

##### Use for simple sub-modules use hooks and use services for complex sub-modules.

##### Track details about sub-modules in help section including, install hooks, required for production, and outputs or alters JSON-LD

##### Create dedicated projects for integration with other ecosystems (i.e, Next.js and Drupal Commerce)

##### Use hooks for simple and basic integrations on a contributed module's behalf
- @see [schemadotorg.schemadotorg.inc](../schemadotorg.schemadotorg.inc)

##### Rely on the default settings for field storage, instance, view, and form displays whenever possible
- Generally, only alter the default configuration when it improves the UI/UX

##### Provide reasonable configuration defaults

##### Ensure that most settings and behaviors are configurable or alterable via hooks

##### Use contributed modules and configuration before writing custom code 

##### During Alpha releases only support the latest stable release of Drupal core.
- TBD What versions of Drupal core should be supported?

##### Always use patch files uploaded to issues on Drupal.org. 
- Do NOT use an MR's diff as a patch because the contents may change.
- @see https://www.drupal.org/docs/develop/git/using-gitlab-to-contribute-to-drupal/downloading-a-patch-file


# 3000 - Schema.org

##### Schema.org should provide 80% of a site's base content architecture and the remaining 20% is custom configuration and code

##### Examples from https://Schema.org should be considered the canonical reference for implementation guidelines

##### Map Drupal entity types to Schema.org types
- User - https://schema.org/Person
- Media - https://schema.org/MediaObject
- Node - https://schema.org/Thing
- Taxonomy Term and Vocabulary - https://schema.org/DefinedTerm and https://schema.org/DefinedTermSet
- Paragraph - https://schema.org/StructuredValue and https://schema.org/Intangible
- Content Block - https://schema.org/WebContent, https://schema.org/Statement,  and https://schema.org/SpecialAnnouncement

##### Use the [United States customary units](https://en.wikipedia.org/wiki/United_States_customary_units) for measurements with [Unit of Measure (UOM)](https://www.doa.la.gov/media/r4roqhpi/unitofmeasurecodes.pdf) codes.

##### Use the [additionalType](https://schema.org/additionalType) property to add more specific types which are not defined by Schema.org
- Use general Schema.org types as additional type when possible.
- Use machine names (i.e., snake_case) for additional type values because
  machine names are Drupal and API best practices and easier to use via
  template suggestions and queries.

##### Use dedicated content types over an [additionalType](https://schema.org/additionalType) when the content type has specific use-case.
- Specific use cases for content types include...
  - Custom content authoring
  - Dedicated access controls
  - Dedicate API endpoint

##### Use common/shared Schema.org properties with best practices
- Use https://schema.org/contactPoint with https://schema.org/contactType for multiple phone numbers and email addresses.
- Use https://schema.org/Role with https://schema.org/Organization to https://schema.org/Person relationships, 
  which includes https://schema.org/actor, https://schema.org/employee, https://schema.org/member, https://schema.org/performer, https://schema.org/provider, https://schema.org/organizer, and https://schema.org/sponsor.
- Use https://schema.org/image for the main image of a https://schema.org/WebPage
- Use https://schema.org/video  for the main video of a https://schema.org/WebPage
- Use https://schema.org/additionalProperty for additional properties
- Use https://schema.org/sameAs for social media links
- Use https://schema.org/relatedLink for other related web pages, including related blog posts. Related links can be personalized and dynamic.
- Use https://schema.org/significantLink for significant URLs on the page, including the content immediately relevant to the current page. Significant links should not be personalized because they are always relevant.
- Use https://schema.org/about  for direct corresponding relationships. Generally, a https://schema.org/CreativeWork should be about only one https://schema.org/Thing.
- Use https://schema.org/mentions for information included in the page's content. Mentions could be extracted from the body's inline links. Mentions should not be personalized.

##### Define inverse of relationships using entity references
- For [Person](https://schema.org/Person), [Place](https://schema.org/Place), and [Organization](https://schema.org/Organziation) relationships 
  - `subOrganization ↔ parentOrganization`: Used to build Organization hierarchy
  - `memberOf ↔ member`: Used to associate a Person with a (conceptual) Organization
- For [CreativeWork](https://schema.org/CreativeWork) relationships
  - `isPartOf ↔ hasPart`: Used to build CreativeWork (with WebPage) parent/child relationships and hierarchies across multiple pages and datasets.
  - `subjectOf ↔ about`: Used to associate a Thing with a CreativeWork  (@see [mentions](https://schema.org/mentions))
  - `mainEntity ↔ mainEntityOfPage`: Used to build CreativeWork parent/child relationships within a single page
- References
  - [Case Study: Does Webpage Schema (About & Mentions) Improve Rankings?](https://inlinks.com/case-studies/case-study-does-webpage-schema-about-mentions-improve-rankings/)

##### Use the highest level Schema.org type and property when possible.
- Use `member ↔ memberOf` instead of `worksFor ↔ employee`
- Use `subOrganization ↔ parentOrganization` instead of `containedInPlace ↔ containsPlace`
- Use [audience](https://schema.org/audience) with [WebPage](https://schema.org/WebPage) and [MedicalWebPage](https://schema.org/MedicalWebPage) and don't use [medicalAudience](https://schema.org/medicalAudience)
- For [PodcastEpisode](https://schema.org/PodcastEpisode), reuse the [isPartOf]https://schema.org/isPartOf) `schema_is_part_of` field but map it to [partOfSeries](https://schema.org/partOfSeries)

##### Allow Schema.org mappings to have additional mappings.
- The main/primary mapping is the equivalent the https://schema.org/mainEntityOfPage.
- All public facing content (a.k.a. nodes) should have https://schema.org/WebPage as an additional mapping.

##### Support [Google Structured Data](https://developers.google.com/search/docs/appearance/structured-data/intro-structured-data)
- Provide reasonable initial support for the common Google Structured Data types.
- Support the below Schema.org types 
  - [Article](https://developers.google.com/search/docs/appearance/structured-data/article)
  - [BreadCrumb](https://developers.google.com/search/docs/appearance/structured-data/breadcrumb)
  - [Course](https://developers.google.com/search/docs/appearance/structured-data/course-info)
  - [FAQ](https://developers.google.com/search/docs/appearance/structured-data/faqpage)
  - [JobPosting](https://developers.google.com/search/docs/appearance/structured-data/job-posting)
  - [LocalBusiness](https://developers.google.com/search/docs/appearance/structured-data/local-business)
  - [Organization](https://developers.google.com/search/docs/appearance/structured-data/organization)
  - [ProfilePage](https://developers.google.com/search/docs/appearance/structured-data/profile-page)
  - [Vehicle](https://developers.google.com/search/docs/appearance/structured-data/vehicle-listing)
  - [Quiz](https://developers.google.com/search/docs/appearance/structured-data/education-qa)
- Support the below Schema.org properties
  - [image](https://developers.google.com/search/docs/appearance/structured-data/article) 1x1, 4x3, and 16x9 sizes. (@see schemadotorg_demo_standard.module)

#### Use a taxonomy vocabulary for property values that need to be update-able, field-able, filter-able, token-able, or hierarchical.

#### Standardize content type properties and settings
- Content type labels should use title case
- Content types should include a description

#### Standardize field properties and settings
- Field names should be singular
- Field labels should use sentence case
- Field labels should attempt to match the field name
- All fields should include a description
- Content type specific field names can be prefixed with the content type's machine name
- For options/lists, allowed values should use snake case for the internal value and sentence case for the displayed text
- Fields should be required if the value is needed to support a feature or key information
- Max lengths should be set for strings that require definitive max lengths
- Set default values when applicable especially for boolean/checkboxes
- Set default settings which required for a field type to work as expected


# 4000 - User experience / Site building

##### Provide a demo of the ideal content authoring experience that can be created via Schema.org Blueprints

##### Content authoring experience takes priority over developer experience

##### Use the main node edit form for structured Schema.org data and the sidebar for meta and configuration data

##### Use external JavasScript libraries as needed to improve UI/UX
- [JsTree](https://www.jstree.com) to display hierarchical relationships
- [MermaidJS](https://github.com/mermaid-js/mermaid) and [Svg-Pan-Zoom](https://github.com/ariutta/svg-pan-zoom) for diagrams
- [CodeMirror](http://codemirror.net) for editing YAML and JSON \

##### Use a page builder (i.e., Layout Paragraphs) and an HTML editor (i.e., CKEditor5) depending on the use case.
- Use an HTML Editor (CKEditor) for
  - Long form mostly text documents
  - Documents that require simple embedded elements which include images, videos, quotes, etc…
  - Content that could be redistributed via an API
- Use page builder (i.e. Paragraph Layouts with Mercury Editor) for
  - Multi-column layouts on a webpage
  - Webpages that require complex widgets including forms, slideshows, tabs, etc…
  - Content that is not generally distributed via an API

##### Fields and field groups should be ordered from general to specific information.
- Common and general fields should be first on node edit forms.
- Uncommon and specific field should be last on node edit forms.

# 5000 - Dependency management

##### Use Drupal core's recommendation for organizing composer.json
- [Issue #2769841: Prefer caret over tilde in composer.json](https://www.drupal.org/project/drupal/issues/2769841)

##### Use composer.libraries.json with the [Composer merge plugin](https://github.com/wikimedia/composer-merge-plugin) 
for managing optional dependencies and patches.
- [Drupal core favors path repository](https://www.drupal.org/node/3069730) 
  but the [composer patches plugin will soon no longer resolve patch dependencies](https://www.cweagans.net/2023/07/dependency-patch-resolution/).

##### External JavaScript libraries will be included via https://cdnjs.cloudflare.com
- Using https://cdnjs.cloudflare.com is the simplest approach
- External libraries are only used by administrators
- A dedicated schemadotorg_libraries.module can be created as dedicated contrib project
  to download and configure external libraries


# 6000 - JSON:API

##### Only expose required and useful endpoints and properties
- Expose all Schema.org types and properties to APIs
- Expose standard and expected entity types and properties to APIs

##### When feasible hide Drupalisms from endpoints and properties
- Remove field_- prefix from properties

##### Avoid deleting API endpoints or properties. Instead, deprecate API endpoints and properties as needed.
- @see [GraphQL Best Practices](https://graphql.org/learn/best-practices/#versioning)

##### Use snake case for API entities and properties.
- This aligns with Drupal's naming conventions


# 7000 - StarterKit and Demo

##### Provide starter kits for common sets of Schema.org types with additional functionality.

##### Allows starter kits to add fields to existing Schema.org types.

##### Provide a demo profile and module that creates the ideal backend content management and authoring experience

##### Starter kits should follow Schema.org types unless there is more common naming convention available.
- place ⇒ location
- physician ⇒ doctor
- person ⇒ profile


# 8000 - Contributed modules and themes

⭐ = Indicates that the Schema.org Blueprints module provides an integration/sub-module.

##### Follow contributed module selection best practices
- Select popular and stable modules when possible.
- Look at popular and supported distributions for suggestions.
- Choose modules that address clearly defined goals of the backend and front-end user experience.

##### Consistently name entity fields using `(field|schema)_{bundle}_{name}` or `(field|schema)_{name}`

##### Use the `schema_` field prefix to distinguish Schema.org properties from other fields.

##### Use field-related modules that structure and manage https://schema.org/DataType and https://schema.org/Intangible.
- [Address](https://www.drupal.org/project/address) ⭐ for https://schema.org/address
- [Corresponding Entity References](https://www.drupal.org/project/cer) ⭐ for https://schema.org/inverseOf
- [Duration Field](https://www.drupal.org/project/duration_field) for https://schema.org/Duration
- [Field Validation](https://www.drupal.org/project/field_validation) ⭐ for https://schema.org/identifier
- [Gender](https://www.drupal.org/project/gender) for https://schema.org/GenderType
- [Geolocation Field](https://www.drupal.org/project/geolocation) ⭐ for https://schema.org/GeoCoordinates.
  - [Geofield](https://www.drupal.org/project/geofield) an alternative for https://schema.org/GeoCoordinates. No integration is being provided.
- [Office Hours](https://www.drupal.org/project/office_hours) ⭐ for https://schema.org/OpeningHoursSpecification
- [Physical Fields](https://www.drupal.org/project/physical) ⭐ for https://schema.org/QuantitativeValue
- [Range](https://www.drupal.org/project/range) ⭐ for https://schema.org/MonetaryAmount
- [SmartDate](https://www.drupal.org/project/smart_date) ⭐ for https://schema.org/Date and https://schema.org/Schedule
- [Time Field](https://www.drupal.org/project/time_field) for https://schema.org/Time

##### Use entity reference-related modules for relationships
- [Existing Values Autocomplete Widget](https://www.drupal.org/project/existing_values_autocomplete_widget) ⭐ for text fields with common values
- [Entity Reference Override](https://www.drupal.org/project/entity_reference_override) ⭐ for https://schema.org/Role relationships.
- [Entity Reference Tree Widget](https://www.drupal.org/project/entity_reference_tree) ⭐ for selecting hierarchical taxonomy terms
- [Inline Entity Form](https://www.drupal.org/project/inline_entity_form) ⭐ for editing concrete and key relations

##### Use embedded content for CKEditor
- [Entity Embed](https://www.drupal.org/project/entity_embed) for building complex structured body content
- Not yet compatible with CKEditor5
  - [Views entity embed](https://www.drupal.org/project/views_entity_embed) for embedding views.

##### Use common SEO modules to improve SEO
- [Simple XML sitemap](https://www.drupal.org/project/simple_sitemap) ⭐ for generating sitemap.xml
  - [XML sitemap](https://www.drupal.org/project/xmlsitemap) an alternative which could be supported
- [Metatag](https://www.drupal.org/project/metatag) ⭐ for providing meta tag support
- [Pathauto](https://www.drupal.org/project/pathauto) ⭐ for URL aliases
- [Redirect](https://www.drupal.org/project/redirect) for redirects

##### Use common API and Headless modules
- [JSON:API Extras](https://www.drupal.org/project/jsonapi_extras) ⭐ to customize JSON:API
- [OpenAPI UI](https://www.drupal.org/project/openapi_ui) for displaying OpenAPI specs.
- [Consumer Image Styles](https://www.drupal.org/project/consumer_image_styles) provide image styles to APIs
- [Decoupled Router](https://www.drupal.org/project/decoupled_router) improve an aliases and redirects for endpoints.
- [Simple OAuth](https://www.drupal.org/project/simple_oauth) for OAuth 2.0 support.

##### For Demo & Starter Kits: Use config management module as needed
- [Configuration Rewrite](https://www.drupal.org/project/config_rewrite) for tweaking existing configuration settings

##### For Demo & Starter Kits: Use site builder tools as needed
- [Automatic Entity Label](https://www.drupal.org/project/auto_entitylabel) ⭐ for computed entity labels for https://schema.org/Person
- [Convert Bundles](https://www.drupal.org/project/convert_bundles) for convert Schema.org types to more specific types
- [Focal Point](https://www.drupal.org/project/focal_point) ⭐ for automated cropping of images
- [Field Group](https://www.drupal.org/project/field_group) ⭐ for grouping related fields
- [Entity Browser](https://www.drupal.org/project/entity_browser) ⭐ with [Entity Browser Enhance(d|r)](https://www.drupal.org/project/entity_browser_enhanced) for providing an entity browser/picker/selector.
- [Entity Prepopulate](https://www.drupal.org/project/epp) ⭐ for prepopulating entity reference via query string parameters
- [Entity Print](https://www.drupal.org/project/entity_print) for printing entities as PDF documents
- [Linkit](https://www.drupal.org/project/linkit) for managing internal links
- [Markup](https://git.drupalcode.org/project/markup) for inline help text for entity forms
- [Link Attributes](https://www.drupal.org/project/link_attributes) for adding attributes to links
- [Quick Node Clone](https://www.drupal.org/project/quick_node_clone) for quickly cloning a node.
- [Token Filter](https://www.drupal.org/project/token_filter) for allowing tokens to be used within a text format
- [Context Stack](https://www.drupal.org/project/context_stack) for accessing entity tokens via the token filter

##### For Demo & Starter Kits: Use Menu enhancements as needed
- [Menu Select](https://www.drupal.org/project/menu_select) for improving node menu select field functionality
  - Alternatives [Node Menu Placer](https://www.drupal.org/project/node_menu_placer) 
    and [Menu tree](https://www.drupal.org/project/menu_tree)

##### For Demo & Starter Kits: Use Views enhancements as needed
- [Better Exposed Filters](https://www.drupal.org/project/better_exposed_filters) form improving a view's exposed filter
- [EVA: Entity Views Attachment](https://www.drupal.org/project/eva) for attaching a view to an entity's display
- [Views Add Button](https://www.drupal.org/project/views_add_button) for including add content buttons to an admin view.
  - [Add Content by Bundle Views Area Plugin ](https://www.drupal.org/project/add_content_by_bundle)
- [Views Bulk Operations (VBO)](https://www.drupal.org/project/views_bulk_operations) of enhancing views bulk operations

##### For Demo & Starter Kits: Use content authoring UX/UI improvement modules as needed
- [Allowed Formats](https://www.drupal.org/project/allowed_formats) ⭐for limiting and simplifying text formats
- [Autosave Form](https://www.drupal.org/project/autosave_form) preventing editors from losing data
- [Chosen](https://www.drupal.org/project/chosen) improving multi-select UX
- [DropzoneJS](https://www.drupal.org/project/dropzonejs) for drag-n-drop file uploads
- To be considered
  - [Same Page Preview](https://www.drupal.org/project/same_page_preview) to allow editors to preview changes on the same page  
    _(Bugs are creating an unexpected UX)_

##### For Demo & Starter Kits: Use CKEditor 5 feature and enhancement modules as needed
- [CKEditor 5 Plugin Pack](https://www.drupal.org/project/ckeditor5_plugin_pack) adds find-and-replace, indent block, and more...
- [CKEditor Anchor Link](https://www.drupal.org/project/anchor_link) adds the anchor link support
- [CKEditor CodeMirror](https://www.drupal.org/project/ckeditor_codemirror) to improve source editing
- [CKEditor Details Accordion](https://www.drupal.org/project/ckeditor_details) for simple accordions
- [CKEditor Link Styles](https://www.drupal.org/project/ckeditor_link_styles) for styling links as buttons
- [CKEditor5 Embedded Content](https://www.drupal.org/project/ckeditor5_embedded_content) ⭐ allows rich content to be inserted into HTML
- [CKEditor5 Fullscreen](https://www.drupal.org/project/ckeditor5_fullscreen) for fullscreen mode
- [CKEditor5 Paste Filter](https://www.drupal.org/project/ckeditor5_paste_filter) to clean-up MS-Word HTML markup

##### For Demo: Use administration improvement modules as needed.
- [Admin Dialogs](https://www.drupal.org/project/admin_dialogs) for opening simple forms and tasks in a dialog (modal).
- [Content Model Documentation](https://www.drupal.org/project/content_model_documentation) ⭐ for displaying entity relationship diagrams (ERD)
- [Dashboards with Layout Builder](https://www.drupal.org/project/dashboards) for providing customizable dashboards to users
- [Environment Indicator](https://www.drupal.org/project/environment_indicator) for displaying the current environment to administrators
- [Type Tray](https://www.drupal.org/project/type_tray) ⭐ for improving the 'Add content' UI/UX
- [Queue UI](https://www.drupal.org/project/queue_ui) for viewing and managing queues
- [Ultimate Cron](https://www.drupal.org/project/ultimate_cron) for viewing and managing cron tasks
- [File Delete](https://www.drupal.org/project/file_delete) for easily deleting files
- [Media file delete](https://www.drupal.org/project/media_file_delete) for deleting the associated file when deleting a media entity.
- [Media Library Media Modify](https://www.drupal.org/project/media_library_media_modify) ⭐ adds the ability to modify the referenced media items.
- [Help Topics](https://www.drupal.org/node/2354963) for better documentation
- [Field Compare](https://www.drupal.org/project/field_compare) for comparing field configuration across content types

##### For Demo: Use translation improvement modules as needed.
- [Admin Toolbar Language Switcher](https://www.drupal.org/project/toolbar_language_switcher) for switching languages via the Gin Admin theme
- [Content Translation Redirect](https://www.drupal.org/project/content_translation_redirect) for redirecting from non-existent translations.

##### For Demo: Use access control and redirect improvement modules as needed.
- [Login Destination](https://www.drupal.org/project/login_destination) redirect authenticated users to the appropriate dashboard
- [Redirect 403 to User Login](https://www.drupal.org/project/r4032login]) redirect access denied pages to user log in. 
- [Redirect Metrics](https://www.drupal.org/project/redirect_metrics) for recording statistics about redirects.
- [Unpublished 404](https://www.drupal.org/project/unpublished_404]) return 404 for unpublished nodes
- [Masquerade](https://www.drupal.org/project/masquerade) for switching users and reviewing access controls

##### For Demo: Use scheduling and content moderation modules as needed
- [Moderation Dashboard](https://www.drupal.org/project/moderation_dashboard) for providing a moderation state dashboard
- [Moderation Sidebar](https://www.drupal.org/project/moderation_sidebar) for quick access to an entity's moderation state
- [Revision Log Default](https://www.drupal.org/project/revision_log_default) for providing default log messages
- [Scheduler](https://www.drupal.org/project/scheduler) ⭐ for scheduling publish and unpublish dates
- [Moderated Content Bulk Publish](https://www.drupal.org/project/moderated_content_bulk_publish) for publishing/unpublishing moderated content

##### For Demo: Use the [Gin Admin Theme](https://www.drupal.org/project/gin) for the administrative UI/UX
- [Gin Moderation Sidebar](https://www.drupal.org/project/gin_moderation_sidebar) themes the Moderation Sidebar module
- [Gin Layout Builder](https://www.drupal.org/project/gin_lb) for layout builder
- [Gin Login](https://www.drupal.org/project/gin_login) for customizing the user login
- [Gin Toolbar ](https://www.drupal.org/project/gin_toolbar)for admin toolbar enhancements
- [Gin Type Tray](https://www.drupal.org/project/gin_type_tray) themes the Type Tray module

##### Use the [Custom Field](https://www.drupal.org/project/custom_field) ⭐ for simple key/value pairs. (i.e., https://schema.org/nutrition)
- Alternatives: [Data field](https://www.drupal.org/project/datafield) and [FlexField](https://www.drupal.org/project/flexfield)

##### Use the [Paragraphs](https://www.drupal.org/project/paragraphs) ⭐ for complex data. (i.e., https://schema.org/HowTo)

##### Use the [Layout Paragraphs](https://www.drupal.org/project/layout_paragraphs) ⭐ for structured data layout
- [Mercury Editor](https://www.drupal.org/project/mercury_editor) ⭐ for effortless, drag-and-drop editing
- Drupal's [Layout Builder](https://www.drupal.org/docs/8/core/modules/layout-builder)
  does not provide easy to understand structured data that be mapped to Schema.org 
