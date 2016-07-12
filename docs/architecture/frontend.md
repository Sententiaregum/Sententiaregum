# Frontend Architecture

The frontend uses a current version of [ReactJS](https://facebook.github.io/react/) and the proprietary flux implementation called [`sententiaregum-flux-container`](https://github.com/Sententiaregum/flux-container). 

## Locations

- Views should be stored in `src/Frontend/components`
- Stores should be stored in `src/Frontend/store`
- Store initializers should be stored in `src/Frontend/store/initializer`
- Store handlers should be stored in `src/Frontend/store/handler`
- Config files (e.g. `menu` config, `routes`, `i18n` files) should be stored in `src/Frontend/config`
- Action creators should be stored in `src/Frontend/actions`
- Utils such as API handlers should be stored in `src/Frontend/util`
- Every module needs its own mocha-based unit-test in `src/Frontend/test`

## Views

The views consist of three kinds of views:

- __Containers:__ such views contain a certain layout (e.g. menu layout, widget layout) and render sub-items into it that they don't know (`this.props.children`).
 They should be as simple as possible and be a functional/stateless component if possible.

- __Stateful components:__ those are the only one that should connect to a store and/or push actions into the dispatcher. The can receive data from the store
 and render the result into the view.

- __Markup components:__ those components can receive a state from a parent component in order to render data into a certain markup. This component can use
 the API of third-party plugins extensively in order to render data into a markup that can be quite complex (if too complex, it should be separated into multiple components).

## Business Logic

A stateful component can run actions using the `flux-container`.
These actions may connect to external services such as the REST API or a `localStorage`. Those calls must not be done directly, but through
custom util objects. The result of this might be handled using a callback or a promise which triggers a dispatch with the given
payload.

If this payload requires some modification logic, it should be stored in its own handler. If this handler is too complex, it should be factored out
into its own module. After that the store could trigger a change which can be subscribed by multiple stateful components (those should use markup components
when rendering more complex data, but pass everything to them using props).

If too much data is in progress, it should be handled by multiple stores organizing themselves using the ordering feature of the dispatcher.
Everyone could handle its own data using a handler and the last store should trigger the change of everything.

For a better documentation, event names should be stored as constants in `src/Frontend/constants` to provide a documentation of events for
one area/feature.

## [Next (Backend Architecture)](https://github.com/Sententiaregum/Sententiaregum/tree/master/docs/architecture/backend.md)
