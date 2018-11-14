<?php

namespace App\Project\NG2019;

use App\Project\AbstractHomeTemplate;

class HomeTemplate extends AbstractHomeTemplate
{
    protected function renderHotelInformation() : string
    {
        return <<<EOT
<legend>Referee Hotel Discounts</legend>
<p>Check back later for discounted hotel room information</p>
EOT;
/*
        return <<<EOT
<legend>Referee Hotel Discounts</legend>
<p>Discounted hotel rates (double occupancy) are now available for the AYSO National Games 2016. </p>
<ul class="cerad-common-help ul_bullets">
<li><a href="http://www.innatboyntonbeach.com/" target="_blank">Inn at Boynton Beach</a> at $65 per night using the code AYSO1660008. This hotel will feature complimentary breakfast.</li>
<li><a href="http://www.hipalmbeachairport.com/" target="_blank">Holiday Inn West Palm Beach Airport</a> at $93 per night plus tax using the code AYSO1660008.</li>
</ul>
<br>
<p>Make your reservations through Global JBS. Contact Information:</p>
<div style="margin:5px 20px">
<p>Trina King<br>
Phone: (561) 290-0587<br>
Email: <a href="mailto:trina@globaljbs.com">trina@globaljbs.com</a><br>
Reservation link: <a href="http://www.globaljbs.com/event/AYSO16" target="_blank">http://www.globaljbs.com/event/AYSO16</a></p>
<br>
</div>
<p>Additional information on booking discounted travel can be found at <a href="http://aysonationalgames.org/book-travel/" target="_blank">http://aysonationalgames.org/book-travel/</a></p>
EOT;
*/
    }

    protected function renderAvailability() : string
    {
        $person = $this->regPerson;
        if (!$person->isReferee) {
            return '';
        }
        $personView = $this->regPersonView;

        return
            <<<EOD
<table class="tableClass">
  <tr><th colspan="2" style="text-align: center;">Availability Information</th></tr>
  <tr><td>Available Wed (Soccerfest) </td><td>{$personView->availWed}     </td></tr>
  <tr><td>Available Thu (Pool Play)  </td><td>{$personView->availThu}     </td></tr>
  <tr><td>Available Fri (Pool Play)  </td><td>{$personView->availFri}     </td></tr>
  <tr><td>Available Sat Morning  (Pool Play)</td><td>{$personView->availSatMorn} </td></tr>
  <tr><td>Available Sat Afternoon(Quarter-Finals)</td><td>{$personView->availSatAfter}</td></tr>
  <tr><td>Available Sun Morning  (Semi-Finals)</td><td>{$personView->availSunMorn }</td></tr>
  <tr><td>Available Sun Afternoon(Finals)</td><td>{$personView->availSunAfter}</td></tr>
  <tr class="trAction"><td class="text-center" colspan="2">
    <a href="{$this->generateUrl('reg_person_update')}">
        Update My Plans or Availability
    </a>
  </td></tr>
</table>
EOD;
    }

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