Table of contents
-----------------

- Introduction
- Features
- Notes


Introduction
------------

The **Schema.org Blueprints Block Content module* integrates content blocks
with the Schema.org Blueprints module.

Content blocks can be used to post a https://schema.org/SpecialAnnouncement, 
see the below Notes.


Features
--------

- Installs the Block content Schema.org mapping type.
- Appends a visible content block's JSON-LD to the page's JSON-LD.
- Ensures that the block content form's vertical tabs and submit button
  are always last.

Notes
-----

### About Special Announcements (a.k.a. Notifications)

**General notes**

- Structured Data and Bing's implementation currently targets Covid-19 
  announcements, but it is a real-world implementation of the specification.
- The SpecialAnnouncement type will likely be used for other notifications in
  the future.
- The SpecialAnnouncement block content type has been added to the 
  [Schema.org Blueprints Hospital Starterkit](https://www.drupal.org/project/schemadotorg_starterkit_hospital) module.

**Implementation notes**

- Site-wide, section, or page-specific notifications should be implemented using content blocks.
- A content block can be mapped to [SpecialAnnouncement](https://schema.org/SpecialAnnouncement)
  or [WebContent](https://schema.org/WebContent)
- The [SpecialAnnouncement](https://schema.org/SpecialAnnouncement) properties 
  and JSON-LD should target Google's
- We can gradually implement more properties as needed

**Recommended properties**

- <https://schema.org/name>
- <https://schema.org/text>
- <https://schema.org/category>
- <https://schema.org/url>
- <https://schema.org/datePosted>
- <https://schema.org/expires>
- <https://schema.org/spatialCoverage>
- <https://schema.org/about>

**References**

- [SpecialAnnouncement - Schema.org Type](https://schema.org/SpecialAnnouncement)
- [COVID-19 Announcements (SpecialAnnouncement) Structured Data | Google Search Central | Documentation](https://developers.google.com/search/docs/appearance/structured-data/special-announcements)
- [Special Announcement specifications - Bing Webmaster Tools](https://www.bing.com/webmasters/help/special-announcement-specifications-5cbd6249)
- [Experimenting with SpecialAnnouncement Markup (Updated June 09, 2020) – Digital.gov](https://digital.gov/2020/05/11/experimenting-with-specialannouncement-markup/) 
