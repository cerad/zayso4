<?php

namespace App\Project\NG2019;

class WelcomeTemplate extends \App\Project\WelcomeTemplate
{
    protected function renderNotes() : string
    {
        $html = <<<EOT
<div id="notes">
EOT;
        $html .= <<<EOT
<p>
  If you just want to peruse the Schedules and Results, no need to go any further.  
  You do not need to sign-in to access Schedules or Results above.  To volunteer, you will need to <a href="{$this->generateUrl('user_create')}">create a zAYSO account</a>.
  In either case, you should
<a href="https://www.rainedout.net/team_page.php?a=0588afab19ee214eca29" target="_blank">subscribe to AYSO National 
Games 2019 text alerts on RainedOut</a>. 
</p>
<br/>
EOT;
        $html .= <<<EOT
<p>
  If you officiated at the 2012 National Games in Tennessee, the 2014 National Games in Southern California, the 2016 
  National Games in West Palm Beach or the 2017/2018 National Open Cup,
  you can simply sign in below and update your plans & availability for the 2019 National Games.
  If you need help remembering your password, 
  you can request help by <a href="{$this->generateUrl('user_password_reset_request')}">clicking here</a>.
</p>
<br/>
<p>
  If this is your first time to the National Games (you are in for a treat), 
  <a href="{$this->generateUrl('user_create')}">click here to create a new zAYSO account</a> 
  and start the registration process to referee or volunteer.
</p>
<br/>
<p>
    If you have previously registered on Blue Sombrero or WuFoo, your registration has been migrated to zAYSO.  <a 
    href="{$this->generateUrl('user_password_reset_request')}">Click here to reset your zAYSO password</a>.
    If you still need help, 
    contact {$this->project->support->name} at 
    <a href="mailto:{$this->project->support->email}">{$this->project->support->email}</a>.
</div>
EOT;
        return $html;
    }
}
