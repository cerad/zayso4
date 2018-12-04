<?php

namespace App\User\Password\ResetRequest;

use App\Core\ActionInterface;
use App\Core\RouterTrait;
use App\Project\Project;
use App\User\Password\PasswordRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PasswordResetRequestAction implements ActionInterface
{
    use RouterTrait;

    private $form;
    private $project;
    private $passwordRepository;


    public function __construct(
        Project $project,
        PasswordRepository $passwordRepository,
        PasswordResetRequestForm $form
    )
    {
        $this->form    = $form;
        $this->project = $project;
        $this->passwordRepository = $passwordRepository;
    }
    public function __invoke(Request $request)
    {
        $form = $this->form;

        $form->handleRequest($request);

        if ($form->isValid()) {

            $formData = $form->getData();

            $user = $this->passwordRepository->findByIdentifier($formData['identifier']);

            $user['passwordToken'] = $this->passwordRepository->generateToken();

            $this->passwordRepository->updatePasswordToken($user['id'],$user['passwordToken']);

            //$this->sendEmail($user);

            return $this->redirectToRoute('user_password_reset_response');
        }
        return new Response($this->render());
    }
    private function render() : string
    {
        $content = <<<EOD
<legend>Request Password Reset</legend>
{$this->form->render()}
EOD;
        return $this->project->pageTemplate->render($content);
    }
    private function sendEmail($user)
    {
        $token = $user['passwordToken'];

        $subject = sprintf('[zAYSOAdmin] Password Reset Request for: %s',$user['name']);

        $body = <<<EOD
A zAYSO password reset request has been made.

Your password reset token is: {$token}

Please enter this token on the site password reset confirmation page.

OR click here: 

{$this->generateUrlAbsoluteUrl('user_password_reset_response',['token' => $token])}
EOD;
        $mailer  = $this->getMailer();

        /** @var \Swift_Message $message */
        $message = $mailer->createMessage();

        $message->setBody($body);

        $message->setSubject($subject);

        $message->setFrom(['noreply@zayso.org' => 'zAYSO Admin']);

        $message->setTo([$user['email'] => $user['name']]);

        $message->setBcc(['ahundiak@gmail.com' => 'Art Hundiak']);
        
        $mailer->send($message);
    }
}