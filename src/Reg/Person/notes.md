We have a User which is basically your UserInterface sort of object.

The original plan was to have a Person entity which existed independently of User
and would constant person oriented data such as ayso id and date of birth.
That pretty much went away and replaced with RegPerson.

So RegPerson links a use to a particular tournament aka project.
Used to be called ProjectPerson.  Still is in fact.

Starting clean with RegPerson and RegPersonRole.

UserProvider uses current project to set roles and registered flag.

For now, query RegPerson whenever it is needed using User.personId and Project.id

Might want to adjust later to allow direct injection?  
