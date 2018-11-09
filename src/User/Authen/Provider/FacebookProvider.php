<?php
namespace App\User\Authen\Provider;

class FacebookProvider extends AbstractProvider
{
    protected $state = 'facebook';
    protected $scope = 'email';

    protected $userInfoUrl      = 'https://graph.facebook.com/me';
    protected $accessTokenUrl   = 'https://graph.facebook.com/oauth/access_token';
    protected $revokeTokenUrl   = 'https://graph.facebook.com/me/permissions';
    protected $authorizationUrl = 'https://www.facebook.com/dialog/oauth';

    /*
     * When using http local host facebook no longer returns email
     * Just name and id
     *
     * Facebook has detected Zayso isn't using a secure connection to transfer information.
     */
}