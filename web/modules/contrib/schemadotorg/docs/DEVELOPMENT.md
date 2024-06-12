Schema.org Blueprints Development
---------------------------------

# Updating between alpha releases

**Some update hooks will be provided between alpha releases.**

Below are the recommended steps for updating between alpha releases.

- Track your changes to all schemadotorg configuration/settings
- Export all schemadotorg configuration.
- Uninstall the schemadotorg module and sub-modules.
- Update the schemadotorg.module.
- Reinstall the schemadotorg module and sub-modules.
- Compare the exported schemadotorg configuration to the new schemadotorg configuration.

If you have not altered any configuration/settings or are okay working with the 
new default configuration. The only configuration you MUST reimport are 
`schemadotorg.schemadotorg_mapping.*.yml` files.

Another alternative is to use the [Configuration Synchronizer](https://www.drupal.org/project/config_sync) module, 
which provides methods for safely importing site configuration from updated
modules, themes, or distributions.

**[Configuration Synchronizer](https://www.drupal.org/project/config_sync) steps**

- Download the Configuration Synchronizer module `ddev composer require --dev 'drupal/config_sync:^3.0@alpha';`
- Enable the Configuration Synchronizer module `ddev drush en -y config_sync;`
- Import exported configuration `ddev drush config:import -y;`
- Update the Schema.org Blueprints related modules. `ddev composer update`
- Execute database updated `ddev drush updb -y;`
- Review, update, and sync imported configuration. (/admin/config/development/distro)
- Export configuration `ddev drush config:export -y;`
- Clean up exported configuration `git diff`
- Commit exported configuration `git commit -am"Update export configuration";`
