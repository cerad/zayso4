Porting ng2016 to Symfony 4

Take max advantage of autowire

Still not using twig

In most cases, inject templates into actions instead of independent views.
Still need to look at actions when can generate multiple formats(html,csv,xls etc).

Use @required in conjunction with traits to eliminate base controller classes.
Look at App\Core\RouterTrait for examples.

Have a project directory for each project containing a Project class along with
other classes such as the master page template.  Goal is to be able to add new
tournaments by adding a new directory.  Ideally there should be no other changed needed.
Env variable CURRENT_PROJECT picks the project.
Typehinting against Project will inject the current project.

ProjectFactory uses the container as a service locator as does the individual projects.
Take a look at using an actual service locator later to restrict access.
ProjectFactory should probable be named to ProjectLocator.

config/packages/parameters.yaml includes secret parameters not suitable for env files.
might eventually just move all env stuff to it.

Use webpack instead of gulp for asset management?
Sticking with gulp for now.  But moved assets into same directory used by encore.
Still need to deal with tournament specific images.

Ongoing issues with unique ids for various entities.
Many entities are unique within a project and have a composite id of projectId:whateverId.
But it does get confusing and have various places in code the explodes the id.  Very hackish.
For now trying to have an actual projectId property in conjunction with the whateverId.

Also want to look at having actual ProjectId types so they can be typehinted against.
But it might more trouble than it's worth at least until php directly supports typed properties.

The user password encoder now uses then latest argon2 hasher.
Legacy password hashes are automatically updated when the user logs in.
