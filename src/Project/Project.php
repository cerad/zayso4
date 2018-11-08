<?php

namespace App\Project;

/**
 * @property-read string $id
 * @property-read string $slug
 * @property-read string $title
 *
 * @property-read ProjectPageTemplate $pageTemplate
 */
class Project
{
    public $id;
    public $slug;
    public $title;

    public $pageTemplate;

    public function __construct()
    {
        $this->pageTemplate = new ProjectPageTemplate($this);
    }
}