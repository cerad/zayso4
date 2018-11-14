<?php

namespace App\Project;

use App\Core\ContainerTrait;
use Psr\Container\ContainerInterface;

/**
 * @property-read string $id
 * @property-read string $slug
 * @property-read string $title
 * @property-read string $version
 * @property-read string $regYear
 *
 * @property-read PageTemplate     $pageTemplate
 * @property-read HomeTemplate     $homeTemplate
 * @property-read WelcomeTemplate  $welcomeTemplate
 *
 * @property-read ProjectContact $support
 *
 * @property-read string $welcomeMessage
 */
abstract class Project
{
    use ContainerTrait;

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

    public function __get($name)
    {
        switch ($name) {

            case 'pageTemplate':
                return $this->container->get($this->pageTemplateClass);

            case 'homeTemplate':
                return $this->container->get($this->homeTemplateClass);

            case 'welcomeTemplate':
                return $this->container->get($this->welcomeTemplateClass);
        }
        return null;
    }
}