For tournaments want the notion of a current project.

Want to make it as easy as possible to inject current project with little or no fuss.

Be nice to typehint against CurrentProject or maybe CurrentProjectInterface

Typehint against Project yields the current project?  Seems hackish but it works okay.

Custom page and welcome templates for each project.

Originally this was developed using the new operator for creating projects and their templates.
However, this meant explicitly passing router, authorization checker and token storage services.
Which was a pain and meant the project factory need more knowledge of the internals than desired.

So refactored to define projects as services and pull them directly from the container.
Projects have to be defined as public and of course global containers are discouraged.
Later on see about replacing the container with a more focused service locator.

