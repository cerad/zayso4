<?php

namespace App\Project;


use App\Core\EscapeTrait;
use App\Project\Project;

class ProjectPageTemplate
{
    use EscapeTrait;

    private $project;
    private $title;
    private $content;

    public function __construct(Project $project)
    {
        $this->project = $project;
        $this->title   = $project->title;
    }
    // Allow for overriding for testing maybe
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function render(string $content): string
    {
        return <<<EOT
<!DOCTYPE html>
<html lang="en">
{$this->renderHead()}        
{$this->renderHeader()}
<body>
  <div id="layout-topmenu">
    {$this->renderTopMenu()}
  </div>
  <div class="container">
    {$content}
  </div>
{$this->renderFooter()}      
{$this->renderScripts()}
</body>
</html>
EOT;
    }

    /*  DOC & Header  */
    private function renderHead() : string
    {
        return <<<EOT

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$this->escape($this->title)}</title>
    <link rel="shortcut icon" type="image/vnd.microsoft.icon" href="/images/favicon.ico">
    <link rel="apple-touch-icon" type="image/png" href="/images/apple-touch-icon-72x72.png"><!-- iPad -->
    <link rel="apple-touch-icon" type="image/png" sizes="114x114" href="/images/apple-touch-icon-114x114.png"><!-- iPhone4 -->
    <link rel="icon" type="image/png" href="/images/apple-touch-icon-114x114.png"><!-- Opera Speed Dial, at least 144?114 px -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/4.1.1/normalize.min.css" media="all" />
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="/css/bootstrap.vertical-tabs.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/4.3.2/css/fileinput.min.css" media="all" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="/css/zayso.css" media="all" />
</head>
EOT;
    }
    private function renderHeader() : string
    {
        return '';
    }
    private function renderFooter() : string
    {
        return '';
    }
    private function renderScripts() : string
    {
        return '';
    }
    private function renderTopMenu() : string
    {
        return '';
    }
}
