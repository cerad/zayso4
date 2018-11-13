<?php

namespace App\Project\NG2019;

class HomeTemplate extends \App\Project\HomeTemplate
{
    protected function renderNotes() : string
    {
        return <<<EOD
<div id="notes">
  <legend>Thank you for registering to Volunteer at the 2019 National Games!</legend>
  <p>
    Review your plans for the National Games to ensure we understand your availability and the roles you expect to play during the Games.
  </p><p>
    Update your plans and availability at any time.
  </p>  
</div>
EOD;
    }
}