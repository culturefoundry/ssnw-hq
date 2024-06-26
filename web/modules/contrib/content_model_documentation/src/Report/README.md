# How to create a new report
Reports use a bit of a basic framework.  It involves the following steps"

1. Create a class in the "Report" directory.
2. It should extend ReportBase and
  implement the ReportInterface and optionally the ReportTableInterface.
3. Create the methods required by the interface(s).
4. Add a menu entry to content_model_documentation.links.menu.yml using the
   "Node Count" as an example.

## Guidance

- Reports should be sorted in the most user friendly way. Machine names are not
  user friendly.
- Give thought to whether the report should be under the "Content model" section
  of the "System" section.
- Tables should provide CSV if possible.
- Links when the hit the CSV should just be URLs, not hrefs, and should be
  absolute so someone can get to the same place provided by a link on the html
  page.
