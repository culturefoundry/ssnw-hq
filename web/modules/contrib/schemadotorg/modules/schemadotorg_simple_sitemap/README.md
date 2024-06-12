Table of contents
-----------------

* Introduction
* Features
* Requirements
* Notes
* Resources

Introduction
------------

The **Schema.org Blueprints Simple Sitemap** automatically adds Schema.org types
to sitemap.xml.


Features
--------

- Automatically adds Schema.org types to sitemap.xml.
- Regenerates sitemap.xml after mapping set is generated.


Requirements
------------

**[Simple Sitemap](https://www.drupal.org/project/simple_sitemap)**  
Generates standard conform hreflang XML sitemaps of the site content and provides a framework for developing other sitemap types.


Notes
-----

Why [Simple XML sitemap](https://www.drupal.org/project/simple_sitemap) over
[XML sitemap](https://www.drupal.org/project/xmlsitemap)?

- Both [XML sitemap](https://www.drupal.org/project/xmlsitemap) and [Simple XML sitemap](https://www.drupal.org/project/simple_sitemap) provide almost identical functionality, and they are easy to configure.
-[Simple XML sitemap](https://www.drupal.org/project/simple_sitemap) has a more extensive installation base for D8+; it targets only D8+, and provides support for Domain Access.
- [XML sitemap](https://www.drupal.org/project/xmlsitemap) has a longer history with more organizations supporting it, but it is unclear what versions are being supported by who.
-[Simple XML sitemap](https://www.drupal.org/project/simple_sitemap) will be easier to install, maintain, and contribute back to.

Details

- It is effortless to switch between 
  [XML sitemap](https://www.drupal.org/project/xmlsitemap)
  and [Simple XML sitemap](https://www.drupal.org/project/simple_sitemap).

- Both provide the identical output.

- The Schema.org integration automatically configures content types to be 
  included in the sitemap.xml as they are created.

- It is the same level of effort to provide and maintain the Schema.org 
  integration for the [XML sitemap](https://www.drupal.org/project/xmlsitemap)
  and [Simple XML sitemap](https://www.drupal.org/project/simple_sitemap)

- The big difference is [Simple XML sitemap](https://www.drupal.org/project/simple_sitemap) 
  is only for D8+. For example, all the documentation and issues for 
  [Simple XML sitemap](https://www.drupal.org/project/simple_sitemap) target 
  a modern Drupal site. [XML sitemap](https://www.drupal.org/project/xmlsitemap) 
  supports multiple Drupal versions with a slightly different sitemap.xml generation strategy.


Resources
---------

- [Drupal 8 SEO: Differences between simple_sitemap and xmlsitemap | gbyte](https://gbyte.dev/blog/drupal8-seo-simple_sitemap-vs-xmlsitemap-differences)
-[A quick guide to Drupal XML Sitemaps (and why you need one) | Specbee](https://www.specbee.com/blogs/quick-guide-drupal-sitemap-modules-xml-sitemap-and-simple-xml-sitemap)
