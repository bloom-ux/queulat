# Generating plugins

You can scaffold a custom post type plugin from the command line using
[WP-CLI](https://wp-cli.org/).

Queulat registers two commands:

* `wp queulat generate cpt-plugin <Class_Name>` — scaffolds a full custom post
  type plugin, including the post type, query and object classes, plus an entry
  file that boots Queulat's autoloader for the generated namespace.
* `wp queulat generate rest-field <Class_Name>` — scaffolds a `Queulat\REST_Field`
  subclass that can extend a WordPress REST API endpoint.

Both commands accept a `--namespace` flag to control the generated namespace
(defaults to `Queulat\CPT` for CPT plugins and `Queulat\REST` for REST fields).

Generated plugins place their PHP classes under an `includes/` directory and use
`Queulat\Helpers\Autoloader` to load them (see
[Autoloading](../README.md#autoloading)).
