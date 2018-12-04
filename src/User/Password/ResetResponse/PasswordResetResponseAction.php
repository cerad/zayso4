<?php
namespace App\User\Password\ResetResponse;

use App\Core\ActionInterface;

use App\Core\RouterTrait;
use App\Project\Project;
use App\User\Password\PasswordRepository;
use App\User\UserEncoder;
use App\User\UserLoginUser;
use App\User\UserProvider;
use AppBundle\Action\Project\User\ProjectUserEncoder;
use AppBundle\Action\Project\User\ProjectUserLoginUser;
use AppBundle\Action\Project\User\ProjectUserProvider;
use AppBundle\Action\Project\User\ProjectUserRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PasswordResetResponseAction implements ActionInterface
{
    use RouterTrait;

    private $project;

    private $userEncoder;
    private $passwordRepository;
    private $userProvider;

    private $userLoginUser;

    private $form;

    private $successRouteName;
    
    public function __construct(
        Project            $project,
        UserEncoder        $userEncoder,
        UserProvider       $userProvider,
        UserLoginUser      $userLoginUser,
        PasswordRepository $passwordRepository,
        PasswordResetResponseForm $form,
        $successRouteName = 'app_home'
    )
    {
        $this->form          = $form;
        $this->project       = $project;
        $this->userEncoder   = $userEncoder;
        $this->userProvider  = $userProvider;
        $this->userLoginUser = $userLoginUser;
        $this->passwordRepository = $passwordRepository;
        $this->successRouteName   = $successRouteName;
    }
    public function __invoke(Request $request, $token)
    {
        $form = $this->form;
        
        $form->setData(['token' => $token]);
        
        $form->handleRequest($request);

        if ($form->isValid()) {

            $formData = $form->getData();

            $user = $this->passwordRepository->findByToken($formData['token']);
            
            $password = $this->userEncoder->encodePassword($formData['password']);

            $this->passwordRepository->changePassword($user['id'],$password);
            
            $user = $this->userProvider->loadUserByUsername($user['username']);
            
            $this->userLoginUser->loginUser($request,$user);
            
            return $this->redirectToRoute($this->successRouteName);
        }
        return new Response($this->render());
    }
    private function render()
    {
        $content = <<<EOD
<legend>Reset Password</legend>
{$this->form->render()}
{$this->renderHelp()}
EOD;
        return $this->project->pageTemplate->render($content);
    }
    private function renderHelp()
    {
        return <<<EOT
<div class="app_help">
  <legend>Not received the password reset token?</legend>
  <ul class="cerad-common-help">
    <li>Check your spam or junk mail folder.</li>
    <li>
      If you still need help, request support by 
      <a href="mailto:{$this->project->support->email}?subject=Password%20Reset%20Help" target="_top">
      clicking here</a>.
    </li>
  </ul>
</div>
EOT;
    }
}