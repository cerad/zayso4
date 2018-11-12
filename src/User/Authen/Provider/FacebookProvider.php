<?php
namespace App\User\Authen\Provider;

class FacebookProvider extends AbstractProvider
{
    protected $state = 'facebook';
    protected $scope = 'email';

    protected $userInfoUrl      = 'https://graph.facebook.com/me?fields=email,name';
    protected $accessTokenUrl   = 'https://graph.facebook.com/oauth/access_token';
    protected $revokeTokenUrl   = 'https://graph.facebook.com/me/permissions';
    protected $authorizationUrl = 'https://www.facebook.com/dialog/oauth';
}