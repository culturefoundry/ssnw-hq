Schema.org Blueprints: Updating
-------------------------------

- [ ] Update `\Drupal\schemadotorg\SchemaDotOrgInstaller::VERSION`
- [ ] Update `data/[VERSION]`
- [ ] Run `ddev drush schemadotorg:download-schema`
- [ ] Run `ddev drush schemadotorg:update-schema`
- [ ] Run `ddev drush schemadotorg:translate-schema`
- [ ] Check [Schema.org: Names overview](https://schemadotorg.ddev.site/admin/reports/schemadotorg/docs/names)
      for issues. 
- [ ] Create update hook to update schema and schemadotorg.names.yml. 
      @see schemadotorg_update_10008()
- [ ] Run PHPUnit tests.
     
