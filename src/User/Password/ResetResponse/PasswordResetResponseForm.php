<?php
namespace App\User\Password\ResetResponse;

use App\Core\AbstractForm;
use App\User\Password\PasswordRepository;
use AppBundle\Action\Project\User\ProjectUserRepository;

use Symfony\Component\HttpFoundation\Request;

class PasswordResetResponseForm extends AbstractForm
{
    private $passwordRepository;
    
    protected $formData = [
        'token'    => null,
        'password' => null,
    ];
    public function __construct(
        PasswordRepository $passwordRepository
    )
    {
        $this->passwordRepository = $passwordRepository;
    }
    public function handleRequest(Request $request)
    {
        if (!$request->isMethod('POST')) return;
        
        $this->isPost = true;
        
        $data = $request->request->all();
        $errors = [];
        
        $token = $this->filterString($data,'token');
        if (!$this->passwordRepository->findByToken($token)) {
            $errors['token'][] = [
                'name' => 'token',
                'msg'  => 'The token does not match any zAYSO accounts.'
            ];
        }
        $password = $this->filterString($data,'password');
        if (strlen($password) < 3) {
            $errors['password'][] = [
                'name' => 'password',
                'msg'  => 'The password is too short.'
            ];
        }
        $this->formData = array_merge($this->formData,[
            'token'    => $token,
            'password' => $password,
        ]);
        $this->formDataErrors = $errors;
    }
    public function render()
    {
        $formData = $this->formData;

        $csrfToken = 'TODO';

        $html = <<<EOD
{$this->renderFormErrors()}
<form role="form" style="width: 400px;" action="{$this->generateUrl('user_password_reset_response')}" method="post">
  <div class="form-group">
    <label for="token">zAYSO Password Reset Token</label>
    <input 
      type="text" id="token" class="form-control" required
      name="token" value="{$this->escape($formData['token'])}" required placeholder="Password Reset Token" />
    {$this->renderFormError('token')}
  </div>
  <div class="form-group">
    <label for="password">New Password</label>
    <input 
      type="text" id="password" class="form-control" required
      name="password" value="" required placeholder="****" />
      {$this->renderFormError('password')}
  </div>
  <input type="hidden" name="_csrf_token" value="{$csrfToken}" />
  <button type="submit" class="btn btn-sm btn-primary submit">
    <span class="glyphicon glyphicon-plus"></span>
    <span>Reset My zAYSO Password</span>
  </button>
</form>
EOD;
        return $html;
    }
}