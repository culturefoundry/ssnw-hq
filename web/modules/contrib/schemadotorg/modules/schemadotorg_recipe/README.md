Table of contents
-----------------

* Introduction
* Features
* Known Issues
* Notes


Introduction
------------

The **Schema.org Blueprints Recipe** module provides a UI
and additional Drush commands for Schema.org Blueprint recipes.


Features
--------

- Provide administrative page to apply/reapply recipes and generate/kill content. 
- Provide Drush commands to apply/reapply recipes and generate/kill content.
- Applies with recipes with a corresponding module that enables dependencies 
  via default configuration and install hook 


References
----------

- https://git.drupalcode.org/project/distributions_recipes/-/blob/1.0.x/docs/recipe_author_guide.md


Known Issues
------------

- [Issue #3478921: A recipe should install new modules in the same way/result as modules installed via the UI or CLI](https://www.drupal.org/project/distributions_recipes/issues/3478921)
- [Issue #3390916: Error when installing a recipe that has configuration files already in the system, even if there is no difference](https://www.drupal.org/project/distributions_recipes/issues/3390916)
- [Issue #3452995: \[Meta\] Support automated tests of recipes](https://www.drupal.org/project/distributions_recipes/issues/3452995)


Notes
-----

Because a recipe installs modules with `$is_syncing = TRUE` 
and only imports a module's simple config before triggering `hook_install()`, 
this is causing unpredictable installation and configuration for 
Schema.org Blueprint modules installed via a recipe.

For example, any Schema.org Blueprint module with a `hook_install()` that alters 
configuration checks makes sure `$is_syncing` is set to FALSE to not overwrite 
any exported and syncing configuration.  
@see schemadotorg_address_install()

Some Schema.org Blueprint modules will install a Schema.org Mapping Type (schemadotorg_mapping_type) 
config entity and use this config entity via hook_install(). 
When a recipe is applied, this will trigger errors because config entities 
in config/install are imported after hook_install() is triggered.
@see schemadotorg_media_install() 

The workaround/solution to fully install a Schema.org Blueprint module
via a recipe is to use a `executeInstallHook` config action via core.extension 
to execute a module's `hook_install()` without `$is_syncing` set to `FALSE`, 
after all the module's configuration is installed.

Below is example of recipe using this workaround/solution.

```
name: 'Schema.org Blueprints Recipe: Example'
install:
  - schemadotorg
  - schemadotorg_media
  - schemadotorg_address
config:
  strict: false
  import:
    schemadotorg: '*'
    schemadotorg_media: '*'
    schemadotorg_address: '*'
  actions:
    core.extension:
      executeInstallHook:
        - schemadotorg_media
        - schemadotorg_address
```

NOTE: This is an ugly workaround which not exactly what config actions 
are intended for.
