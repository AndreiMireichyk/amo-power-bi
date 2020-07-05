<?php

namespace App\Models\Amo;


use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\OAuth2\Client\Provider\AmoCRMException;
use Illuminate\Database\Eloquent\Model;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;

class AmoClient extends Model
{
    public function getClient()
    {
        $client = new AmoCRMApiClient(
            $this->getAttribute('client_id'),
            $this->getAttribute('secret'),
            route('amo.redirect', $this->getAttribute('slug'))
        );

        $this->setAttribute('client', $client);

        return $client;

    }


    /**
     * @return AccessToken
     * @throws \Exception
     */
    public function getToken()
    {
        if (!$this->getAttribute('access_token')) {
            throw new \Exception('Invalid access token');
        }

        return new AccessToken([
            'access_token' => $this->getAttribute('access_token'),
            'refresh_token' => $this->getAttribute('refresh_token'),
            'expires' => $this->getAttribute('expires'),
            'baseDomain' => $this->getAttribute('baseDomain'),
        ]);
    }


    /**
     * @throws \Exception
     */
    public function checkToken()
    {
        $accessToken = $this->getToken();

        $this->client->setAccessToken($accessToken)
            ->setAccountBaseDomain($accessToken->getValues()['baseDomain'])
            ->onAccessTokenRefresh(
                function (AccessTokenInterface $accessToken, string $baseDomain) {
                    $this->fill([
                        'access_token' => $accessToken->getToken(),
                        'refresh_token' => $accessToken->getRefreshToken(),
                        'expires' => $accessToken->getExpires(),
                        'baseDomain' => $baseDomain,
                    ])->save();
                }
            );
    }
}
