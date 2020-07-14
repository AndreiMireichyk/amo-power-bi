<?php


namespace App\Models\AmoCrm;


use AmoCRM\Client\AmoCRMApiClient;
use Illuminate\Support\Facades\Storage;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;

trait AmoCrmAuth
{
    private function setClient()
    {
        $client = new AmoCRMApiClient(
            $this->getAttribute('client_id'),
            $this->getAttribute('secret'),
            route('amo.redirect', $this->getAttribute('slug'))
        );

        return $this->client = $client;
    }

    /**
     * @return AccessToken
     * @throws \Exception
     */

    private function prepareToken()
    {
        if (!$this->getAttribute('access_token')) {
            throw new \Exception('Invalid access token');
        }

        return new AccessToken([
            'access_token' => $this->getAttribute('access_token'),
            'refresh_token' => $this->getAttribute('refresh_token'),
            'expires' => $this->getAttribute('expires'),
            'baseDomain' => $this->getAttribute('base_domain'),
        ]);
    }


    /**
     * @throws \Exception
     */
    private function checkToken()
    {
        $accessToken = $this->prepareToken();

        $this->client->setAccessToken($accessToken)
            ->setAccountBaseDomain($accessToken->getValues()['baseDomain'])
            ->onAccessTokenRefresh(
                function (AccessTokenInterface $accessToken, string $baseDomain) {

                    $this->fill([
                        'access_token' => $accessToken->getToken(),
                        'refresh_token' => $accessToken->getRefreshToken(),
                        'expires' => $accessToken->getExpires()
                    ])->save();
                }
            );
    }
}
