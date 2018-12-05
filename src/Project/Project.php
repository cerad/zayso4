<?php

namespace App\Project;

/**
 * @property-read string $id
 * @property-read string $slug
 * @property-read string $title
 * @property-read string $version
 * @property-read string $regYear
 *
 * @property-read AbstractPageTemplate     $pageTemplate
 * @property-read AbstractHomeTemplate     $homeTemplate
 * @property-read AbstractWelcomeTemplate  $welcomeTemplate
 *
 * Maybe have a contacts collection?
 * @property-read ProjectContact $support
 * @property-read ProjectContact $scheduler
 * @property-read ProjectContact $assignor
 * @property-read ProjectContact $administrator
 * @property-read ProjectContact $system
 *
 * @property-read string $welcomeMessage
 *
 *  @property-read array $formControls
 *  @property-read array $regPersonFormControls
 */
abstract class Project implements ProjectInterface
{
    protected $projectLocator;

    public $id;
    public $slug;
    public $title;

    public $regYear;

    // Contacts
    public $support;
    public $scheduler;
    public $assignor;
    public $administrator;
    public $system;

    public $version;

    public $welcomeMessage; // Welcome to blah blah

    protected $pageTemplateClass;
    protected $homeTemplateClass;
    protected $welcomeTemplateClass;

    public $formControls = [];
    public $regPersonFormControls = [];

    public function __construct(ProjectLocator $projectLocator)
    {
        $this->projectLocator = $projectLocator;

        $this->initFormControls();
        $this->initRegPersonFormControls();

    }
    abstract protected function initRegPersonFormControls() : void;

    public function __get($name)
    {
        switch ($name) {

            case 'pageTemplate':
                return $this->projectLocator->get($this->pageTemplateClass);

            case 'homeTemplate':
                return $this->projectLocator->get($this->homeTemplateClass);

            case 'welcomeTemplate':
                return $this->projectLocator->get($this->welcomeTemplateClass);
        }
        return null;
    }
    public function mergeFormControls(array $controls) : array
    {
        $merged = [];
        foreach($controls as $key => $meta) {
            if (!isset($meta['type'])) {
                $map = isset($meta['map']) ? $meta['map'] : $key;
                $meta = \array_merge($meta,$this->formControls[$map]);
            }
            $merged[$key] = $meta;
        }
        return $merged;
    }
    // Move this to it's own class
    protected function initFormControls() : void
    {
        $this->formControls = [
            'regName' => [
                'type'        => 'text',
                'label'       => 'Registration Name',
                'placeHolder' => 'Your Name',
                'required'    => true,
                'constraints' => ['unique_project_name_transformer'],
            ],
            'regEmail' => [
                'type'        => 'email',
                'label'       => 'Registration Email',
                'placeHolder' => 'Your Email',
                'required'    => true,
                'constraints' => ['email_constraint'],
            ],
            'regPhone' => [
                'type'        => 'phone',
                'label'       => 'Mobile Phone',
                'placeHolder' => 'Your Phone Number',
                'required'    => false,
                'transformer' => ['phone_transformer'],
            ],
            'willVolunteer' => [
                'type'    => 'select',
                'label'   => 'Will Volunteer',
                'default' => 'no',
                'choices' => ['no' => 'No', 'yes'=> 'Yes (besides refereeing)', 'maybe' => 'Not Sure',],
            ],
            'willCoach' => [
                'type'    => 'select',
                'label'   => 'Will Coach',
                'default' => 'no',
                'choices' => ['no' => 'No', 'yes'=> 'Yes (or assist)', 'maybe' => 'Not Sure',],
            ],
            'willReferee' => [
                'type'    => 'select',
                'label'   => 'Will Referee',
                'default' => 'no',
                'choices' => ['no' => 'No', 'yes'=> 'Yes', 'maybe' => 'Not Sure',],
            ],
            'willAttend' => [
                'type'    => 'select',
                'label'   => 'Will Attend',
                'default' => 'na',
                'choices' => [
                    'na'    => 'Not yet answered',
                    'no'    => 'No',
                    'yes'   => 'Yes - For Sure',
                    'yesx'  => 'Yes - If my team is selected',
                    'maybe' => 'Maybe',
                ],
            ],
            'refereeBadgeAYSO' => [
                'type'    => 'select',
                'label'   => 'AYSO Referee Badge',
                'default' => 'na',
                'choices' => [
                    'None'         => 'NA',
                    'Regional'     => 'Regional',
                    'Intermediate' => 'Intermediate',
                    'Advanced'     => 'Advanced',
                    'National'     => 'National',
                    'National_1'   => 'National 1',
                    'National_2'   => 'National 2',
                    'Assistant'    => 'Assistant',
                    'U8Official'   => 'U8 Official',
                ],
            ],
            'shirtSize' => [
                'type'    => 'select',
                'label'   => 'T-Shirt Size',
                'default' => 'na',
                'choices' => [
                    'na'         => 'na',
                    'youths'     => 'Youth Small',
                    'youthm'     => 'Youth Medium',
                    'youthl'     => 'Youth Large',
                    'adults'     => 'Adult Small',
                    'adultm'     => 'Adult Medium',
                    'adultl'     => 'Adult Large',
                    'adultlx'    => 'Adult Large X',
                    'adultlxx'   => 'Adult Large XX',
                    'adultlxxx'  => 'Adult Large XXX',
                    'adultlxxxx' => 'Adult Large XXXX',
                ],
            ],
            'notesUser' => [
                'type'  => 'textarea',
                'label' => 'Notes',
                'rows'  =>  5,
                'cols'  => 40,
            ],
            'notes' => [
                'type'  => 'textarea',
                'label' => 'Notes',
                'rows'  =>  5,
                'cols'  => 60,
            ],
            'fedIdAYSO' => [
                'type'        => 'text',
                'label'       => 'AYSO Volunteer ID',
                'href'        => 'eayso.org',
                'placeHolder' => '8 digit number',
                'transformer' => 'ayso_volunteer_key_transformer',
                'constraints' => ['aysoid_constraint'],
            ],
            'orgIdAYSO' => [
                'type'        => 'test',
                'label'       => 'AYSO Region Number',
                'href'        => 'eayso.org',
                'placeHolder' => '1-4 digit number',
                'transformer' => 'ayso_region_key_transformer',
                'constraints' => ['aysoid_constraint'],
            ],
            'availSatMorn' => [
                'type'    => 'select',
                'label'   => 'Available Saturday Morning (Pool Play)',
                'default' => 'no',
                'choices' => ['no' => 'No', 'yes' => 'Yes', 'maybe' => 'Maybe'],
            ],
            'availSatAfter' => [
                'type'    => 'select',
                'label'   => 'Available Saturday Afternoon (Qtr-Finals)',
                'default' => 'no',
                'choices' => ['no' => 'No', 'yes' => 'Yes', 'maybe' => 'Maybe'],
            ],
            'availSunMorn' => [
                'type'    => 'select',
                'label'   => 'Available Sunday Morning (Semi-Finals)',
                'default' => 'no',
                'choices' => ['no' => 'No', 'yes' => 'Yes', 'maybe' => 'Maybe'],
            ],
            'availSunAfter' => [
                'type'    => 'select',
                'label'   => 'Available Sunday Afternoon (Finals)',
                'default' => 'no',
                'choices' => ['no' => 'No', 'yes' => 'Yes', 'maybe' => 'Maybe'],
            ],
            'availWed' => [
                'type'    => 'select',
                'label'   => 'Available Wednesday (Soccerfest)',
                'default' => 'no',
                'choices' => ['no' => 'No', 'yes' => 'Yes', 'maybe' => 'Maybe'],
            ],
            'availThu' => [
                'type'    => 'select',
                'label'   => 'Available Thursday (Pool Play)',
                'default' => 'no',
                'choices' => ['no' => 'No', 'yes' => 'Yes', 'maybe' => 'Maybe'],
            ],
            'availFri' => [
                'type'    => 'select',
                'label'   => 'Available Friday (Pool Play)',
                'default' => 'no',
                'choices' => ['no' => 'No', 'yes' => 'Yes', 'maybe' => 'Maybe'],
            ],
        ];
    }
}