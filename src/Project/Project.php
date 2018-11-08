<?php

namespace App\Project;

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


    public function __construct()
    {
        $this->pageTemplate = new ProjectPageTemplate($this);
    }
}