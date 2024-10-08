## Continuous Integration Configuration

This directory provides a location for site-specific overrides by the Continuous Integration system, Concourse.

This list will grow over time.

If a given file does not exist, a Freelock default will be used.

### Currently defined/used:

- ci/feature-exclude -- a single-line, space-delimited list of features to not revert on deployment
- ci/skip-copy -- Create this file to skip copying the database down from production. Remove it to allow db copies.
- ci/mods-enable -- Space-delimited list of modules to enable on Stage after a db copy, during the sanitization step. Default "reroute_email"
- ci/mods-disable -- Space-delimited list of modules to disable on Stage after a db copy, during the sanitization step. Default "google_analytics piwik securepages"
- ci/acquia-alias -- set to an "application name" for Acquia sites, which use dev/test/live env names.
- ci/acquia-local-stage -- custom override to use a Freelock stage copy and not Acquia Test. Added to support a specific client.
- ci/pantheon-alias -- set to the alias used by Terminus to access a Pantheon site.
- ci/production -- Production deploy mode. See below...
- ci/hold -- a list of modules to not automatically update/hold back
- ci/skip-site-mode -- Do not automatically set performance features (css/js aggregation) (D8 sites only)
- ci/sites.json -- Provide custom paths for multi-site, domain, or a docroot in a subdirectory. See below...
- ci/skip-sanitize -- Skip the drush sqlsan command that rewrites email addresses. If there is an executable ci/sanitize.sh shell script, it will still be run.


### Production deploy modes

The contents of the ci/production file controls the production deployment job. Set its contents to a single word as follows:

- "auto" or "yes" -- Deploy code and config to production, don't use maintenance mode. Notify when done.
- "maintenance", "offline" -- Set site into maintenance mode before deploying. Then deploy code and config. Then put site back online and notify.
- "skip-config" -- Deploy code but do not apply config.
- "manual" -- Set site into maintenance mode, deploy code and config, and notify. Leave in maintenance mode. Unlock with "drush online."
- file not present, "no", or anything else -- Bump version number, send notification, and exit. Do not deploy code or config.

This value can be changed for a specific release. Its value will be reported in the site status.

### Sites.json

The sites.json file must be valid json. The outermost layer is an object wrapping a list of objects. One of the objects should have the key "default" and will be used in tests as the main site. Additional objects may be added to handle multi-site/multi-domain sites, and should have a key that represents the site -- this will be used in test artifacts.

Each site object should have at least these keys:

- site -- repeat the site key so the object may be sent without its key
- prod -- URL to use as "prod"
- stage -- URL to use as "stage"
- dev -- URL to use as dev

In addition, you can also specify:

- docroot -- relative path from the git root for docroot -- this is where behat expects to find behat.yml.
- database -- For multi-sites, you must add this to each site to trigger a database copy and separate clean check. If it exists, it triggers this behavior.

### Current Multisite support:

These jobs should be multi-site aware:

- check-clean
- export-db-prod
- import-db-stage
- export-db-stage
- import-db-dev
- run-behat
- run-wraith
- apply-config-stage
- apply-config-prod

TODO:

This job is not yet multi-site-aware:

- sanitize-stage

... also, with multi-site the default site is now optional -- however if omitted, the run-behat job breaks if the docroot is needed, and at least one site needs to have a database entry.
