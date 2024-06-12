Table of contents
-----------------

* Introduction
* Features
* Configuration


Introduction
------------

The **Schema.org Blueprints Options module** manages allowed values 
for option based fields.


Features
--------

- Defines allowed values for Schema.org properties.
- Removes allowed values suffix for Schema.org properties. 
  (i.e. Removes 'Diet' suffix from https://schema.org/RestrictedDiet allowed values)
- Defines range includes https://schema.org/Enumeration for Schema.org properties.
- Convert https://schema.org/Enumeration into allowed values.
- Auto assigns allowed value function for Schema.properties range includes.


Configuration
-------------

- Go to the Schema.org properties configuration page.  
  (/admin/config/schemadotorg/settings/properties#edit-schemadotorg-options)
- Go to the 'Options settings' details.
- Enter Schema.org properties with allowed values.


Notes
-----

Sources for allowed values in [schemadotorg_options.settings.yml](config%2Finstall%2Fschemadotorg_options.settings.yml)

- [dosageForm](https://schema.org/dosageForm)
  @see https://www.fda.gov/industry/structured-product-labeling-resources/dosage-forms
