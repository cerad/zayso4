<?php
namespace App\User\Create;

use App\Core\ActionInterface;
use App\Core\GuidTrait;
use App\Core\RouterTrait;
use App\Project\Project;
use App\User\User;
use App\User\UserConnection;
use App\User\UserEncoder;
use App\User\UserLoginUser;
use App\User\UserProvider;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserCreateAction implements ActionInterface
{
    use GuidTrait;
    use RouterTrait;

    private $conn;
    private $project;
    private $userEncoder;
    private $userProvider;
    private $userLoginUser;
    private $userCreateForm;

    public function __construct(
        Project        $project,
        UserConnection $conn,
        UserProvider   $userProvider,
        UserEncoder    $userEncoder,
        UserLoginUser  $userLoginUser,
        UserCreateForm $userCreateForm
    )
    {
        $this->conn            = $conn;
        $this->project         = $project;
        $this->userEncoder     = $userEncoder;
        $this->userProvider    = $userProvider;
        $this->userLoginUser   = $userLoginUser;
        $this->userCreateForm  = $userCreateForm;
    }
    public function __invoke(Request $request)
    {
        $userData = [
            'name'     =>  null,
            'email'    =>  null,
            'password' =>  null,
            'role'     => 'ROLE_USER',
        ];
        $userCreateForm = $this->userCreateForm;
        $userCreateForm->setData($userData);
        
        $userCreateForm->handleRequest($request);
        if ($userCreateForm->isValid()) {

            $userData = $userCreateForm->getData();

            $user = $this->createUser(
                $userData['name'],
                $userData['email'],
                $userData['password'],
                $userData['role']
            );
            $this->userLoginUser->loginUser($request,$user);

            return $this->redirectToRoute('reg_person_register');
        }
        $request->attributes->set('userCreateForm',$userCreateForm);
        
        return new Response($this->render());
    }
    private function createUser(string $name,string $email,string $password,string $role) : User
    {
        // Encode password
        //$salt     = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $password = $this->userEncoder->encodePassword($password);

        // Derive username from email?
        $emailParts = explode('@',$email);
        $username  = $emailParts[0];
        $username = $this->generateUniqueUsername($username);

        // Person guid
        $personId = $this->generateGuid();

        // The insert - todo UserInserter?
        $qb = $this->conn->createQueryBuilder();
        
        $qb->insert('users');

        $qb->values([
            'name'      => ':name',
            'email'     => ':email',
            'username'  => ':username',
            'personKey' => ':personKey',
            'password'  => ':password',
            'roles'     => ':roles',
        ]);

        $qb->setParameters([
            'name'      => $name,
            'email'     => $email,
            'username'  => $username,
            'personKey' => $personId,
            'password'  => $password,
            'roles'     => $role,
        ]);
        $qb->execute();

        return $this->userProvider->loadUserByUsername($email);
    }
    // Todo maybe move to repository or something
    private function generateUniqueUsername(string $username) : ?string
    {
        $sql = 'SELECT id FROM users WHERE username = ?';
        $stmt = $this->conn->prepare($sql);

        $cnt = 1;
        $usernameTry = $username;
        while(true) {
            $stmt->execute([$usernameTry]);
            if (!$stmt->fetch()) {
                return $usernameTry;
            }
            $cnt++;
            $usernameTry = $username . $cnt;
        }
        return null;
    }
    private function render() : string
    {
        $content = <<<EOD
<legend>Create a zAYSO Account</legend>
{$this->userCreateForm->render()}
<br/><br />
<!--
<h4>Do you have a Google Account?</h4>
<br/>
<a href="#" class="btn btn-sm btn-default" role="button">
  <span class="glyphicon glyphicon-plus"></span> 
  Sign up with Google
</a>
<br/><br/>
<h4>Do you have a Facebook Account?</h4>
<br/>
<a href="#" class="btn btn-sm btn-default" role="button">
  <span class="glyphicon glyphicon-plus"></span> 
  Sign up with Facebook
</a>
<br/><br/>
-->
<legend>Already have a zAYSO account? </legend>
<a href="{$this->generateURL('app_welcome')}" class="btn btn-sm btn-primary" role="button">
  <span class="glyphicon glyphicon-edit"></span> 
  Sign in
</a>
EOD;
        return $this->project->pageTemplate->render($content);
    }
}
