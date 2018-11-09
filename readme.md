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

Had to jump through some serious hoops to inject router and auth checker into project template.
Need to go back and make them services.
Possibly tag them as projects and a service locator into ProjectFactory

config/packages/parameters.yaml includes secret parameters not suitable for env files.
might eventually just move all env stuff to it.

Use webpack instead of gulp for asset management?

