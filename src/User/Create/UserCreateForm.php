<?php
namespace App\User\Create;

use App\Core\AbstractForm;
use App\User\UserConnection;
use Symfony\Component\HttpFoundation\Request;

class UserCreateForm extends AbstractForm
{
    private $conn;

    public function __construct(UserConnection $conn)
    {
        $this->conn = $conn;
    }
    public function handleRequest(Request $request)
    {
        if (!$request->isMethod('POST')) return;
        $this->isPost = true;
        
        $data = $request->request->all();
        $errors = [];

        $name = $this->filterString($data,'name');
        if ($name === null) {
            $errors['name'][] = [
                'name' => 'name',
                'msg'  => 'Name cannot be blank.'
            ];
        }
        $email  = $this->filterEmail($data,'email');
        $errors = $this->validateEmail($email,$errors);

        $password = $this->filterString($data,'password');
        if ($password === null) {
            $errors['password'][] = [
                'name' => 'password',
                'msg'  => 'Password cannot be blank.'
            ];
        }
        $this->formData = array_merge($this->formData,[
            'name'     => $name,
            'email'    => $email,
            'password' => $password,
        ]);
        $this->formDataErrors = $errors;
    }
    private function validateEmail($email,$errors)
    {
        if ($email === null) {
            $errors['email'][] = [
                'name' => 'email',
                'msg'  => 'Email cannot be blank.'
            ];
            return $errors;
        }
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors['email'][] = [
                'name' => 'email',
                'msg'  => 'Email is not valid.'
            ];
            return $errors;
        }
        // Unique
        $sql = 'SELECT id FROM users WHERE email = ? OR username = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$email,$email]);
        if ($stmt->fetch()) {
            $errors['email'][] = [
                'name' => 'email',
                'msg'  => 'Email is already being used.'
            ];
            return $errors;
        }
        return $errors;
    }
    public function render()
    {
        $formData = $this->formData;

        $csrfToken = 'TODO';

        $html = <<<EOD
<form role="form" style="width: 300px;" action="{$this->generateUrl('user_create')}" method="post" novalidate>
  <div class="form-group">
    <label for="user_create_name">Name</label>
    <input 
      type="text" id="user_create_name" class="form-control" required
      name="name" value="{$formData['name']}" required placeholder="Buffy Summers" />
      {$this->renderFormError('name')}
  </div>
  <div class="form-group">
    <label for="user_create_email">Email</label>
    <input 
      type="email" id="user_create_email" class="form-control" required
      name="email" value="{$formData['email']}" placeholder="buffy@sunnydale.org" />
      {$this->renderFormError('email')}
  </div>
  <div class="form-group">
    <label for="user_create_password">Password</label>
    <input 
      type="password" id="user_create_password" class="form-control" required
      name="password" value="" required placeholder="********" />
      {$this->renderFormError('password')}
  </div>
  <input type="hidden" name="_csrf_token" value="{$csrfToken}" />
  <button type="submit" class="btn btn-sm btn-primary submit">
    <span class="glyphicon glyphicon-plus"></span> Create New zAYSO Account
  </button>
</form>

EOD;
        return $html;
    }
}