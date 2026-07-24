# Changelog

All notable changes to this project are documented in this file.

## Unreleased

### Breaking changes
- Generated custom post type plugins now place their PHP classes under an `includes/` directory instead of `src/`. Regenerate existing plugins or move their files to the new location to stay compatible with the autoloader.
- `Queulat\Post_Type` now exposes an `init()` method that registers all hooks and replaces the old static hook wiring. Generated plugins (and custom subclasses) must call `init()` during bootstrapping instead of relying on `add_action( 'init', ... )` with `register_post_type`.
- `declare(strict_types=1);` is now enabled across all plugin files. Consumer code must respect strict typing when calling into Queulat APIs.
- The plugin and shared helper exit early when accessed directly via the filesystem, following WordPress security best practices. Directly including these files outside of WordPress will now terminate execution.

### Added
- Introduced a Symfony Dependency Injection container with dedicated service providers for assets, forms, and generator services.
- Added a namespaced, WordPress-style autoloader that resolves `class-`, `interface-`, `trait-`, and `enum-` files and is available to generated plugins.
- Generated CPT plugins now ship with a service provider automatically registered via `queulat_service_providers`, exposing their post type, query, and object classes through the container once Queulat boots it on `plugins_loaded`.
- New `Queulat\Contracts\Post_Type_Interface` enforces the public API required by post type implementations.
- New `Queulat\Forms\Node_Factory_Call_Type` enum replaces string constants for argument handler call strategies.
- Introduced interfaces for metaboxes (`Queulat\\Contracts\\Metabox_Interface`), post objects (`Queulat\\Contracts\\Post_Object_Interface`), and post queries (`Queulat\\Contracts\\Post_Query_Interface`) with corresponding class implementations updated accordingly.
- Added a `wp queulat generate rest-field` CLI command to scaffold REST field classes extending the base `Queulat\\REST_Field`.

### Deprecated
- Directly calling a generated `*_Post_Type::register_post_type()` without instantiating is still supported but will no longer register hooks automatically. Use the new `init()` workflow instead.

### Changed
- Queulat defers container construction to `plugins_loaded` (priority 99) so third-party plugins can register services before compilation.
- CPT generator stubs load classes through the shared autoloader and gracefully fall back to `require_once` for the new `includes/` directory.
- Asset enqueuing, admin generator setup, and WP-CLI command registration now resolve dependencies through the container instead of instantiating classes ad hoc.
