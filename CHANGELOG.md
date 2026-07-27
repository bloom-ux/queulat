# Changelog

All notable changes to this project are documented in this file.

## Unreleased

### Breaking changes
- Generated custom post type plugins now place their PHP classes under an `includes/` directory instead of `src/`. Regenerate existing plugins or move their files to the new location to stay compatible with the autoloader.
- `Queulat\Post_Type` now exposes an `init()` method that registers all hooks and replaces the old static hook wiring. Generated plugins (and custom subclasses) must call `init()` during bootstrapping instead of relying on `add_action( 'init', ... )` with `register_post_type`.
- `declare(strict_types=1);` is now enabled across all plugin files. Consumer code must respect strict typing when calling into Queulat APIs.
- The plugin and shared helper exit early when accessed directly via the filesystem, following WordPress security best practices. Directly including these files outside of WordPress will now terminate execution.

### Added
- Added a namespaced, WordPress-style autoloader that resolves `class-`, `interface-`, `trait-`, and `enum-` files and is available to generated plugins.
- New `Queulat\Contracts\Post_Type_Interface` enforces the public API required by post type implementations.
- New `Queulat\Forms\Node_Factory_Call_Type` enum replaces string constants for argument handler call strategies.
- Introduced interfaces for metaboxes (`Queulat\\Contracts\\Metabox_Interface`), post objects (`Queulat\\Contracts\\Post_Object_Interface`), and post queries (`Queulat\\Contracts\\Post_Query_Interface`) with corresponding class implementations updated accordingly.
- Added a `wp queulat generate rest-field` CLI command to scaffold REST field classes extending the base `Queulat\\REST_Field`.
- Added `wp scaffold queulat-cpt-plugin` and `wp scaffold queulat-rest-field` aliases so Queulat generators are discoverable under WP-CLI's built-in `scaffold` namespace while preserving the original `wp queulat generate` commands.
- Added `Queulat\Contracts\Hookable_Interface` and the `queulat_register_hooks()` helper to register hooks for hookable components. `Queulat\Post_Type` and `Queulat\REST_Field` now implement this interface.

### Deprecated
- Directly calling a generated `*_Post_Type::register_post_type()` without instantiating is still supported but will no longer register hooks automatically. Use the new `init()` workflow instead.

### Removed
- Removed the experimental Symfony Dependency Injection container and its assets, forms, and generator service providers. Queulat now wires services directly in `Queulat\Bootstrap`.

### Changed
- Relaxed the Twig dependency constraint from `~3.11.0` to `^3`.
- CPT generator stubs load classes through the shared autoloader and gracefully fall back to `require_once` for the new `includes/` directory.

### Fixed
- Fixed multisite plugin activation by correcting per-site initialization in `Queulat\Post_Type`.
- Fixed return types for the is-email validation rule and the abstract admin submenu page handling.
- Restored node factory argument initialization in the bootstrap.
- Improved WP-CLI command documentation and the CPT plugin generator output.
- Fixed the REST field stub template.
