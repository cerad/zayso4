<?php

namespace App\Project;


use App\Core\EscapeTrait;
//use App\Project\Project;

class ProjectPageTemplate
{
    use EscapeTrait;

    protected $project;
    protected $title;
    protected $content;

    protected $showHeaderImage = false;

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
    protected function renderHead() : string
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
    protected function renderHeader()
    {
        if (!$this->showHeaderImage) {
            $html = <<<EOT
<div id="banner">
  <h1>
    <a href="http://www.aysonationalgames.org/" target="_blank">
      <img src="/images/ng2019/icon.png" height="30" alt="National Games">
    </a>
    {$this->escape($this->title)}
  </h1>
</div>
EOT;
        } else {
            $html = <<<EOT
<div class="skBanners">
  <a href="http://www.aysonationalgames.org/" target="_blank">
    <img class="width-90" src="/images/ng2019/banner.png">
  </a>
  <div class="skFont width-90 display:inline-block">
    AYSO WELCOMES YOU TO WAIPIO PENINSULA SOCCER COMPLEX, WAIPAHU, HAWAII, June 30 - July 7, 2019
  </div>
</div>
EOT;
        }
        return $html;
    }
    protected function renderFooter()
    {
        $support = $this->project->support;

        return <<<EOT
<div class="cerad-footer">
  <br />
  <hr>
  <p> zAYSO - For assistance contact {$support->name} at
    <a href="mailto:{$support->email}?subject={$support->subject}">{$support->email}</a>
      or {$support->phone} 
  </p>
  <p>Version {$this->project->version}</p>
</div>
<div class="clear-both"></div>
EOT;
    }
    protected function renderScripts()
    {
        return <<<EOT
<!-- Placed at the end of the document so the pages load faster -->
<!-- Latest compiled and minified JQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<!-- Bootstrap core JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/4.3.2/js/fileinput.min.js"></script>
<!-- compiled project js -->
<script src="/js/zayso.js"></script>
EOT;
    }

    private function renderTopMenu() : string
    {
        return '';
    }
}
