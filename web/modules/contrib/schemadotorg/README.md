Table of contents
-----------------

* Introduction
* Requirements
* Installation
* Configuration
* Ecosystem


Introduction
------------

> The **Schema.org Blueprints module** provides perfect data structures (Schema.org),
> pristine APIs (JSON:API), and great SEO (JSON-LD).

The [Schema.org Blueprints](https://www.drupal.org/project/schemadotorg) module
uses [Schema.org](https://schema.org) as the blueprint for a Drupal website's
content architecture and structured data.

The best way to get started using the Schema.org Blueprints module is to read
about [Schema.org](https://schema.org) and browse the available
[schemas](https://schema.org/docs/schemas.html).

Once you understand Schema.org, please watch a
[short overview](https://youtu.be/XkZP6QjJkWs) or
[full demo](https://youtu.be/_kk97O1SEw0) of the Schema.org Blueprints module.

Additional documentation

- [docs/DECISIONS.md](https://git.drupalcode.org/project/schemadotorg/-/blob/1.0.x/docs/DECISIONS.md)
- [docs/ROADMAP.md](https://git.drupalcode.org/project/schemadotorg/-/blob/1.0.x/docs/ROADMAP.md)
- [docs/MODULES.md](https://git.drupalcode.org/project/schemadotorg/-/blob/1.0.x/docs/MODULES.md)


Features
--------

General

- Installs Schema.org's data from a CSV into Drupal
- Provides form elements for editing configuration settings 
  and Schema.org autocompletion
- Manages reusable JavaScript libraries for UI and sub-modules
- Removes all dependencies from starter kits and demos to make them
  easy to uninstall.
- Adds the Schema.org Blueprints logo to all 'schemadotorg' routes.
- Adds fragment/hash to a sub-module's configure link so that
  administrators can directly access a sub module's configuration.
  (@see /admin/modules)

Schema.org

- Exposes Schema.org types and properties to Drupal modules
- Defines Schema.org mapping and mapping type entities
- Allows Schema.org types, properties, and names to be configured
- Ensures that Schema.org's naming conventions works with Drupal's internal
  naming conventions

Mappings

- Builds entity types and fields from Schema.org types and properties
- Provides Drush commands to create and delete Schema.org mappings
- Manages configuration edit form for all sub-modules.

Plugins

- Provides Schema.org type entity selection plugin
- Provides Schema.org type Views filter plugin

Contributed Modules  
(@see schemadotorg.schemadotorg.inc)

- **[Duration](https://www.drupal.org/project/duration_field)**  
  Sets duration granularity to hours and minutes.
- **[Content Browser](https://www.drupal.org/project/content_browser)**  
  Uses the content browser for node entity references.
- **[Focal Point](https://www.drupal.org/project/focal_point)**
  Ensures that existing entity image fields use focal point.
- **[Linkit](https://www.drupal.org/project/linkit)**  
  Uses linkit for the link URL autocompletion.
- **[Media Library Media Modify](https://www.drupal.org/project/media_library_media_modify)**  
  Defaults all 'Media' (reference) fields to use the 'Media with contextual modifications' field type.


Requirements
------------

This module requires the Field, Text, and Options modules included
with Drupal core.


Installation
------------

Install the Schema.org Blueprints module as you would normally
[install a contributed Drupal module](https://www.drupal.org/node/1897420).

Use the included [composer.libraries.json](https://git.drupalcode.org/project/schemadotorg/-/blob/1.0.x/composer.libraries.json)
file to quickly install sub-module dependencies.

As your Schema.org Blueprints project evolves, you may want to copy and adjust
the dependencies from the composer.libraries.json file into your project's
root composer.json.

[Watch how to install and set up the Schema.org Blueprints module](https://www.youtube.com/watch?v=Dludw8Eomh4)

---

Below is an example of what must be added to your projects composer.json file
to include Schema.org Blueprints dependencies. Adjust it to match the location 
of your contrib modules directory.

```
{
    "minimum-stability": "dev",
    "require": {
        "schemadotorg/schemadotorg/": "~1.0",
        "wikimedia/composer-merge-plugin": "^2.0"
    },
    "config": {
        "allow-plugins": {
            "cweagans/composer-patches": true,
            "wikimedia/composer-merge-plugin": true
        },
    },
    "extra": {
        "merge-plugin": {
            "include": [
                "web/modules/contrib/schemadotorg/composer.libraries.json",
            ],
            "merge-extra": true,
            "merge-extra-deep": true
        }
    }
}
```

View a diff of the changes in [install/composer.example.json](install/composer.example.json)

- [install/composer.example.json.txt](install/composer.example.json.txt)
- [install/composer.example.json.html](install/composer.example.json.html)

To understand and enable Schema.org Blueprints sub-modules
enable the Schema.org Blueprints Help module and go to (/admin/help)

Use the below Drush command to enable all sub-modules

```
drush pm-list --format=list | grep schemadotorg | xargs drush install -y
```


Configuration
-------------

- Configure 'Schema.org Blueprints' administer permission.  
  (/admin/people/permissions/module/schemadotorg)
- Review Schema.org types configuration.  
  (/admin/config/schemadotorg/settings/types)
- Review Schema.org properties configuration.  
  (/admin/config/schemadotorg/settings/properties)
- Review Schema.org naming conventions configuration.  
  (/admin/config/schemadotorg/settings/names)
- Review Schema.org mappings.  
  (/admin/config/schemadotorg)
- Review Schema.org mapping types.  
  (/admin/config/schemadotorg/types)


Ecosystem
---------

The [Schema.org Blueprints module](https://www.drupal.org/project/schemadotorg)
comprises [50+ sub-modules](https://git.drupalcode.org/project/schemadotorg/-/blob/1.0.x/docs/MODULES.md) 
that provide integrations with various contributed modules to provide the best-structured data with the ideal content authoring and administration user
experiences. Besides sub-modules, other Schema.org Blueprints projects support 
complex, experimental, and deprecated integrations. Schema.org Blueprint 
starter kits provide support for different industries and use cases. 
Additionally, there is a full feature demo of the 
entire Schema.org Blueprints ecosystem.

All [sub-modules include README.md files](https://git.drupalcode.org/project/schemadotorg/-/blob/1.0.x/docs/MODULES.md) describing the 
sub-module's use case, features, dependencies, and configuration. 
Every sub-module includes extensive test coverage, which makes it easier 
for people to contribute code to fix or improve an integration or feature set.

The Schema.org Blueprints module provides pristine APIs; for integration with a decoupled/headless front-end. 
Similarly, there is an 
[experimental project](https://www.drupal.org/project/schemadotorg_experimental) 
for supporting the Mercury Editor with Layout Paragraphs. Lastly, 
deprecated sub-modules and integrations live (and die) in the 
[Schema.org Blueprints Deprecated project](https://www.drupal.org/project/schemadotorg_deprecated).

Schema.org Blueprints Starter Kits extend the generated Schema.org types to include additional functionality, including default configuration, views, and SEO URLs. 
Starter kits include support for 
[events](https://www.drupal.org/project/schemadotorg_starterkit_events),
[podcasts](https://www.drupal.org/project/schemadotorg_starterkit_podcast),
[recipes](https://www.drupal.org/project/schemadotorg_starterkit_recipes),
[organizations](https://www.drupal.org/project/schemadotorg_starterkit_organization),
[hospitals](https://www.drupal.org/project/schemadotorg_starterkit_hospital),
and [medical information](https://www.drupal.org/project/schemadotorg_starterkit_medical).

Finally, the [Schema.org Blueprint Demo project](https://www.drupal.org/project/schemadotorg_demo) and installation profile provide an 
opinionated demo of the Schema.org Blueprints ecosystem built on top 
of Drupal’s standard profile.
