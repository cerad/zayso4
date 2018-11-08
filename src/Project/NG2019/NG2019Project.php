<?php

namespace App\Project\NG2019;

use App\Project\Project;
use App\Project\ProjectContact;

class NG2019Project extends Project
{
    public $id    = 'AYSONationalGames2019';
    public $slug  = 'ng2019';
    public $title = 'NG2019';

    public $version = '2018-11-08';

    public function __construct()
    {
        parent::__construct();

        $this->support = new ProjectContact('Art Hundiak','ahundiak@gmail.com','256-457-5943','NG2019 zAYSO question...');
        $this->system  = new ProjectContact('Zayso Admin','noreply@zayso.org','');

        $this->scheduler = new ProjectContact(
            'Robert McCarthy',
            'soccer.ref62@yahoo.com',
            '808-286-9280',
            'NG2019 schedule question...');

        $this->assignor = clone $this->scheduler;
        $this->assignor->setSubject('NG2019 Referee Assignments');

        $this->administrator = clone $this->scheduler;
        $this->administrator->setSubject('NG2019 Referee Administrator');


    }
}