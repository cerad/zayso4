<?php

namespace App\Project;

use App\Core\AuthorizationTrait;
use App\Core\EscapeTrait;
use App\Core\RouterTrait;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ProjectPageTemplate
{
    use EscapeTrait;
    use RouterTrait;
    use AuthorizationTrait;

    protected $project;
    protected $title;
    protected $content;

    protected $showHeaderImage  = false;
    protected $showResultsMenu  = true;
    protected $showScheduleMenu = true;
    protected $showFinalResults = true;

    public function __construct(Project $project, RouterInterface $router, AuthorizationCheckerInterface $authChecker)
    {
        $this->project = $project;
        $this->title   = $project->title;

        $this->router      = $router;
        $this->authChecker = $authChecker;
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
    protected function renderHeader() : string
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
    protected function renderFooter() : string
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
    protected function renderScripts() : string
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
    protected function renderTopMenu() : string
    {
        $html = <<<EOT
<nav class="navbar navbar-default">          
  <div class="navbar-header">
    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#topmenu">
       <span class="sr-only">Toggle navigation</span>
       <span class="icon-bar"></span>
       <span class="icon-bar"></span>
       <span class="icon-bar"></span>
     </button>
   </div>  <!-- navbar-header -->
           
   <!-- Collect the nav links, forms, and other content for toggling -->
   <div id="topmenu" class="collapse navbar-collapse">
EOT;
        $html .= $this->renderMenuForGuest();

        //$html .= $this->renderMenuForUser();

        $html .= <<<EOT
  </div><!-- navbar-collapse -->
</nav>
EOT;
        return $html;
    }
    protected function renderMenuForGuest() : string
    {
        $html = <<<EOT
<ul class="nav navbar-nav">
EOT;
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $html .= <<<EOT
{$this->renderHome()}
EOT;
        } else {
            $html .= <<<EOT
{$this->renderWelcome()}
EOT;
        }
        $html .= <<<EOT
{$this->renderTopMenuSchedule()}
{$this->renderTopMenuResults()}
{$this->renderTopMenuTextAlerts()}
</ul>
EOT;
        return $html;
    }
    protected function renderHome() : string
    {
        return <<<EOT
<li>
  <a href="{$this->generateUrl('app_home')}">HOME</a>
</li>
EOT;
    }
    protected function renderWelcome() : string
    {
        return <<<EOT
<li>
  <a href="{$this->generateUrl('app_welcome')}">WELCOME</a>
</li>
EOT;
    }
    protected function renderAdmin() : string
    {
        return <<<EOT
<li>
  <a href="{$this->generateUrl('app_admin')}">ADMIN</a>
</li>
EOT;
    }
    protected function renderTopMenuSchedule() : string
    {
        if (!$this->showScheduleMenu) {
            return '';
        }
        return <<<EOT
<li class="dropdown">
  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">SCHEDULES <span class="caret"></span></a>
  <ul class="dropdown-menu">
    <li><a href="{$this->generateUrl('schedule_game' )}">GAME    SCHEDULES</a></li>
    <li><a href="{$this->generateUrl('schedule_team' )}">TEAM    SCHEDULES</a></li>
  </ul>
</li>
EOT;
    }
    protected function renderTopMenuTextAlerts() : string
    {
        $html = <<<EOT
<li><a href="{$this->generateUrl('app_text_alerts')}">TEXT ALERTS</a></li>
EOT;
        return $html;
    }
    protected function renderTopMenuResults() : string
    {
        if (!$this->showResultsMenu) {
            return '';
        }

        $html = <<<EOT
<li class="dropdown">
  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">RESULTS <span class="caret"></span></a>
  <ul class="dropdown-menu">
    <li><a href="{$this->generateUrl('results_poolplay')}">POOL PLAY</a></li>
    <li><a href="{$this->generateUrl('results_medalround')}">MEDAL ROUND</a></li>
    <li><a href="{$this->generateUrl('results_sportsmanship')}">SPORTSMANSHIP</a></li>
EOT;
        if ($this->isGranted('ROLE_ADMIN') OR $this->showFinalResults) {
            $html .= <<<EOT
<li><a href="{$this->generateUrl('results_final')}">FINAL STANDINGS</a></li>
EOT;
        }
        $html .= <<<EOT
  </ul>
</li>
EOT;
        return $html;
    }
}
