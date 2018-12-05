<?php
namespace App\Reg\Person\Transformer;

use App\Reg\Person\RegPerson;

/**
 * This is actually a view transformer and not a standard data transformer
 * probably does not belong here
 * but okay for now I guess.
 *
 * Maybe rename to WillRefereeViewTransformer
 */
class WillRefereeTransformer
{
    public function __invoke(RegPerson $person)
    {
        $willReferee = isset($person->plans['willReferee']) ? $person->plans['willReferee'] : 'no';
        $willReferee = strtolower(($willReferee));
        
        switch($willReferee)
        {
            case 'yes':
                $willRefereeView = 'Yes';
                break;
            case 'maybe':
                $willRefereeView = 'Maybe';
                break;
            default:
                $willRefereeView = 'No';
        }
        $badge = isset($person->roles['ROLE_REFEREE']) ?
            $person->roles['CERT_REFEREE']->badge :
            null;

        $badgeUser = isset($person->roles['ROLE_REFEREE']) ?
            $person->roles['CERT_REFEREE']->badgeUser :
            null;

        if ($willReferee !== 'no') {
            if ($badge && ($badge === $badgeUser)) {
                $willRefereeView = sprintf('%s (%s)',$willRefereeView,$badge);
            }
            if ($badge && ($badge !== $badgeUser)) {
                $willRefereeView = sprintf('%s (%s or %s)',$willRefereeView,$badge,$badgeUser);
            }
        }
        return $willRefereeView;
    }
}