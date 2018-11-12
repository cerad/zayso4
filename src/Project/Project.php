<?php

namespace App\Project;

use Psr\Container\ContainerInterface;

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
abstract class Project
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

    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container    = $container;
    }
    public function __get($name)
    {
        switch ($name) {

            case 'pageTemplate':
                return $this->container->get($this->pageTemplateClass);

            case 'welcomeTemplate':
                return $this->container->get($this->welcomeTemplateClass);
        }
        return null;
    }
}