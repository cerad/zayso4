<?php

namespace App\Core;

use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

/**
 * This is probably overkill but I wanted an example of using the service subscriber
 * Basically lazy loads the mailer object
 */
class MailerLocator implements ServiceSubscriberInterface
{
    private $locator;

    public function __construct(ContainerInterface $locator)
    {
        $this->locator = $locator;
    }
    public function get() : \Swift_Mailer
    {
        return $this->locator->get('mailer');
    }
    public static function getSubscribedServices()
    {
        return [
            'mailer' => \Swift_Mailer::class, // could have used 'mailer' as well
        ];
    }
}