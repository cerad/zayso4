<?php

namespace App\Project\NG2019;

use App\Project\Project;
use App\Project\ProjectContact;
use Psr\Container\ContainerInterface;

class NG2019Project extends Project
{
    public $id    = 'AYSONationalGames2019';
    public $slug  = 'ng2019';
    public $title = 'NG2019';

    public $version = '2018-11-08';

    public $welcomeMessage = 'Welcome to the AYSO National Games 2019';

    protected $pageTemplateClass    = 'App\\Project\\NG2019\\PageTemplate';
    protected $welcomeTemplateClass = 'App\\Project\\NG2019\\WelcomeTemplate';

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->support = new ProjectContact('Art Hundiak','ahundiak@gmail.com','256-457-5943','NG2019 zAYSO question...');
        $this->system  = new ProjectContact('Zayso Admin','noreply@zayso.org','');

        $this->scheduler = new ProjectContact(
            'Robert McCarthy',
            'soccer.ref62@yahoo.com',
            '808-286-9280',
            'NG2019 schedule question...');

        $this->assignor = $this->scheduler->withSubject('NG2019 Referee Assignments');

        $this->administrator = $this->scheduler->withSubject('NG2019 Referee Administrator');

    }
}