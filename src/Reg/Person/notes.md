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

ProjectId,PersonId form an unique compound key however
database still uses autoinc for primary key.

And then we sometimes used ProjectId:PersonId as a key and exploded it.
Don't want to mess with the schema for now.
Have to wait and see how the games stuff hooks in.

# Registration Form

Registering a user for a given project is one of the more complex processes.
It's also functionality that needs to be available early on and tends to be hacked together a bit.

Each project will select what data is needed to register.
Meta data for project specific form controls is stored in $project->regPersonFormControls

Master definitions for form controls are stored in $project->formControls.
These are basically default templates for common form controls such as name and email.

If a transformer is available then the appropriate data transformer is applied as the form is processed.

A form control unique to a specific project can be fully define in regPersonFormControls
in which case it will a type attribute as well as any other needed information.

If a map sttribute is present then it is used to merge the master form control.
This allows mapping controls such as refereeBadge to ayso or ussf badges.

Otherwise the key of the registration form control is used to merge the master definition of the control.

After building a local list os form controls, a formData structure is generated with default values for each control.
This simplifies subsequent mapping as we know there will be a formData entry for each control.
Grouping is also implemented at this point so all plans and availability can be stored as an array.

It is important that all the form control keys match the data returned by the RegPerson::toArray.
This is where things can get a bit dicey as things need to stay in sync.
And while not directly related, they also need to match the database stuff.

All of this form control stuff occurs in the forms's constructor so default formData is available to the action.

