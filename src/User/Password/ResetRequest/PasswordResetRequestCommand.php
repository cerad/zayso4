<?php
namespace App\User\Password\ResetRequest;

use App\User\UserProvider;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
//  Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PasswordResetRequestCommand extends Command
{
    private $mailer;
    private $userProvider;

    public function __construct(
        \Swift_Mailer $mailer,
        UserProvider  $userProvider)
    {
        parent::__construct();
        
        $this->mailer       = $mailer;
        $this->userProvider = $userProvider;
    }
    protected function configure()
    {
        $this
            ->setName('user:password:reset:request')
            ->setDescription('Reset user password.')
            ->addArgument('username', InputArgument::REQUIRED, 'Zayso username or email');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');

        echo sprintf("Reset user password: %s.\n",$username);

        $this->sendEmail($username);

        echo sprintf("Reset user password email sent.\n");
    }
    private function sendEmail($username)
    {
        $mailer = $this->mailer;

        $subject = sprintf('[zAYSO) Password reset request for %s',$username);
        
        $message = $mailer->createMessage()
            ->setSubject($subject)
            ->setFrom('ahundiak@gmail.com')
            ->setTo  ('ahundiak@gmail.com')
            ->setBody('WTF')
        ;

        $status = $mailer->send($message);

        $transport = $mailer->getTransport();
        if (!$transport instanceof \Swift_Transport_SpoolTransport) {
            echo sprintf("Not a spool transport\n");
            return;
        }
        $spool = $transport->getSpool();
        if (!$spool instanceof \Swift_MemorySpool) {
            echo sprintf("Not a spool memory\n");
            return;
        }
        //$spool->flushQueue($this->transportReal);

        echo sprintf("Message class %s %s %s %s %d\n",
            get_class($message),get_class($mailer),
            get_class($spool),get_class($transport),
            $status);
    }
 }
