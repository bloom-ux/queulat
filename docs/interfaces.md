# Interfaces

## Contracts

Queulat defines explicit contracts under the `Queulat\Contracts` namespace for
its main building blocks. Implementing these interfaces lets your classes plug
into Queulat's helpers and generators:

* `Post_Type_Interface` — the public API expected from post type definitions.
* `Post_Query_Interface` — the API for custom post queries.
* `Post_Object_Interface` — the API for custom post objects.
* `Metabox_Interface` — the API for metabox definitions.
* `REST_Field_Interface` — the API for REST API field extensions.
* `Asset_Loader_Interface` — the API for asset loaders (see [Loading assets](assets.md)).
* `Hookable_Interface` — any class that can register its own WordPress hooks via an `init()` method. `Queulat\Post_Type` and `Queulat\REST_Field` implement it.

### Registering hooks for hookable components

The `queulat_register_hooks()` helper calls `init()` on any `Hookable_Interface`
instance, which is the recommended way to wire a component's actions and filters:

```php
use function Queulat\queulat_register_hooks;

queulat_register_hooks( new My_Post_Type() );
```

## Node_Interface

| Related Interfaces | Related Traits |
| ------------------ | -------------- |
| `Component_Interface` | `Node_Trait` |
| `Element_Interface` | `Childless_Node_Trait` |

Nodes are the lowest level of objects that should be used with forms

Use `Node_Trait` to help implement this interface or `Childless_Node_Trait`. In general terms,
elements should use the former and components the latter.

### Component_Interface

Extends the Node_Interface and Attributes_Interface.

### Element_Interface

Extends the Node_Interface and Attributes_Interface. Also, adds the `get_tag_name` method.

#### HTML_Element_Interface

Extends the Element_Interface and Properties_Interface.

##### Form_Element_Interface

Extends HTML_Element_Interface and Form_Node_Interface.

## Attributes_Interface

"Attributes" are special properties used by objects implementing this interface. They're rendered as HTML attributes `key="val"`

Use `Attributes_Trait` to help implement this interface.

## Form_Node_Interface

Objects implementing this interface (elements or components) are used as form objects. They have a "name" which is used to send data to the server, a "value" and a "label".

The `Form_Control_Trait` helps implementing the "label" and "name" getters and setters from this interface.
The "value" getter and setter should be defined by your own custom component.

## Node_List_Interface

Extends ArrayAccess, SeekableIterator, Countable, Serializable.

Most commonly used to get an array-like set of children from a Node.

## Option_Node_Interface

Used by controls such as checkboxes, radios, selects and every component where the user is presented with several alternatives they can choose from.

Use `Options_Trait` to help implement this interface.

## Properties_Interface

Node properties (not to be confused with regular object properties) can store arbitrary data, such as view settings, error data or validation state.

Use `Properties_Trait` to help implement this interface.

## View_Interface

Base interface to be used by form views.
