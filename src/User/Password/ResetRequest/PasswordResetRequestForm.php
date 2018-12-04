<?php
namespace App\User\Password\ResetRequest;

use App\Core\AbstractForm;
use App\User\Password\PasswordRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class PasswordResetRequestForm extends AbstractForm
{
    private $authenticationUtils;
    private $passwordRepository;
    
    protected $formData = [
        'identifier' => null,        
    ];
    public function __construct(
        AuthenticationUtils $authenticationUtils,
        PasswordRepository $passwordRepository
    )
    {
        $this->authenticationUtils = $authenticationUtils;
        $this->passwordRepository  = $passwordRepository;
    }
    public function handleRequest(Request $request)
    {
        if (!$request->isMethod('POST')) return;
        
        $this->isPost = true;
        
        $data = $request->request->all();
        $errors = [];
        
        $identifier = $this->filterString($data,'identifier');

        if (!$this->passwordRepository->findByIdentifier($identifier)) {
            $errors['identifier'][] = [
                'name' => 'identifier',
                'msg'  => 'The email does not match any zAYSO accounts.'
            ];

        }
        $this->formData = array_merge($this->formData,[
            'identifier' => $identifier,
        ]);
        $this->formDataErrors = $errors;
    }
    public function render()
    {
        $formData = $this->formData;
        
        $identifier = $formData['identifier'] ? : $this->authenticationUtils->getLastUsername();
        
        $csrfToken = 'TODO';

        $html = <<<EOD
<form role="form" style="width: 300px;" action="{$this->generateUrl('user_password_reset_request')}" method="post">
  <div class="form-group">
    <label for="identifier">zAYSO Email</label>
    <input 
      type="text" id="identifier" class="form-control" required
      name="identifier" value="{$this->escape($identifier)}" required placeholder="zAYSO Email" />
      {$this->renderFormError('identifier')}
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