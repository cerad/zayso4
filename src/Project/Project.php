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
 * @property-read ProjectPageTemplate $pageTemplate
 *
 * @property-read ProjectContact $support
 */
class Project
{
    public $id;
    public $slug;
    public $title;

    public $pageTemplate;

    // Contacts
    public $support;
    public $scheduler;
    public $assignor;
    public $administrator;
    public $system;

    public $version;


    public function __construct(RouterInterface $router, AuthorizationCheckerInterface $authChecker)
    {
        $this->pageTemplate = new ProjectPageTemplate($this,$router,$authChecker);
    }
}