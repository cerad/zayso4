<?php

namespace App\Project;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @property-read string $id
 * @property-read string $slug
 * @property-read string $title
 * @property-read string $version
 *
 * @property-read PageTemplate     $pageTemplate
 * @property-read WelcomeTemplate  $welcomeTemplate
 *
 * @property-read ProjectContact $support
 *
 * @property-read string $welcomeMessage
 */
class Project
{
    public $id;
    public $slug;
    public $title;

    // Contacts
    public $support;
    public $scheduler;
    public $assignor;
    public $administrator;
    public $system;

    public $version;

    public $welcomeMessage; // Welcome to blah blah

    protected $pageTemplateClass;
    protected $welcomeTemplateClass;

    protected $router;
    protected $authChecker;

    public function __construct(RouterInterface $router, AuthorizationCheckerInterface $authChecker)
    {
        $this->router = $router;
        $this->authChecker = $authChecker;
        //$this->pageTemplate = new ProjectPageTemplate($this,$router,$authChecker);
    }
    public function __get($name)
    {
        switch ($name) {

            case 'pageTemplate':
                return new $this->pageTemplateClass($this, $this->router, $this->authChecker);

            case 'welcomeTemplate':
                return new $this->welcomeTemplateClass($this, $this->router);
        }
        return null;
    }
}