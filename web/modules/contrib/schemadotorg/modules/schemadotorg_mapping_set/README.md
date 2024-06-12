Table of contents
-----------------

* Introduction
* Features
* Configuration
* Notes


Introduction
------------

The **Schema.org Blueprints Mapping Sets module** provides the ability to create
a set of related Schema.org types via Drush.

Use cases include...

- **Common** (People, events, places, and organizations)
- **Blocks** (Teasers, quotes, and components)
- **How to** (Step-by-step guides)
- **Food** (Restaurants and menus)
- **Entertainment**	(Movies, TV, and podcasts)
- **Web**	(Webpage, FAQ, and slideshows)
- **Education**	(Schools and course)
- **Organization**	(Hours, ratings, services, job posting and businesses)
- **Medical organization**	(Physician, clinics, labs, hospitals, tests, and audience)
- **Medical information** (Conditions, symptoms, risks, causes, tests, procedures, and trials)


Features
--------

- Admin page to set up, generate, kill, and teardown mapping sets
- Drush commands to set up, generate, kill, and teardown mapping sets
- Configure custom mapping sets.
- Displays a warning when a mapping is created, which is part of a mapping set.


Configuration
-------------

- Go to the Schema.org Mapping Sets configuration page.  
  (/admin/config/schemadotorg/sets/settings)
- Enter Schema.org mapping sets.


Notes
-----

Schema.org mapping set provides a quick way to set up and test different
related Schema.org types while adjusting and refining your Schema.org types
and property defaults.

Custom modules can also use the 'schemadotorg\_mapping\_set.manager' service
to set up Schema.org types via code.
