Table of contents
-----------------

* Introduction
* Features
* Requirements
* Use cases
* Todo


Introduction
------------

The **Schema.org Blueprints Smart Date module** allows a Smart Date field to be
used to create date ranges and event schedules included in a site's
Schema.org JSON-LD.

Currently, the best use case for the Smart date is the pending
[eventSchedule](https://schema.org/eventSchedule) schema.


Features
--------

- During installation, alters the Schema.org Blueprints properties configuration
  to use the [eventSchedule](https://schema.org/eventSchedule) property.

- Alters Schema.org JSON-LD for startDate and eventSchedule to convert
  Smart Date recurring dates and overrides in Schedule types.


Requirements
------------

**[Smart date](https://www.drupal.org/project/smart_date)**    
Provides the ability to store start and end times, with duration. Also provides an intelligent admin UI, and a variety of formatting options.


Use cases
---------

Smart date range field are appropriate for any Schema.org type that has
a [startDate](https://schema.org/startDate) and
[endDate](https://schema.org/endDate) or an
[eventSchedule](https://schema.org/eventSchedule) property.

The most common use case for the Smart date range field is
an [Event](https://schema.org/Event). There are two approaches to using a
Smart date range field in an Event.

**APPROACH 1:** Use the Smart date range field for the
[startDate](https://schema.org/startDate) property
and do NOT set an [endDate](https://schema.org/endDate) or
[duration](https://schema.org/duration)

**APPROACH 2: \[RECOMMENDED\]** Use the Smart date range field for the
[eventSchedule](https://schema.org/eventSchedule) property
and do NOT set a [startDate](https://schema.org/startDate),
[endDate](https://schema.org/endDate) or
[duration](https://schema.org/duration).
The [eventSchedule](https://schema.org/eventSchedule)
provides the best support for multiple and recurring events.

_The [eventSchedule](https://schema.org/eventSchedule) property is proposed for full integration into Schema.org._

To configure either approach, you need to adjust the
'Default Schema.org type properties'
(/admin/config/schemadotorg/settings/type)
and
'Default Schema.org property fields'
(/admin/config/schemadotorg/settings/properties).


TODO
----

- Make sure all recurring smart date patterns are support via JSON-LD.

- Make sure timezones are correctly supported.
