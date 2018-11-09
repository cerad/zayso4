<?php

namespace App\Project\NG2019;

class PageTemplate extends \App\Project\PageTemplate
{
    protected $showHeaderImage  = false;
    protected $showResultsMenu  = true;
    protected $showScheduleMenu = true;
    protected $showFinalResults = true;

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
}
