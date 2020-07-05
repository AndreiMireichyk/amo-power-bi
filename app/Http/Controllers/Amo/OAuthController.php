<?php

namespace App\Http\Controllers\Amo;

use App\Http\Controllers\Controller;
use App\Models\Amo\AmoClient;
use Illuminate\Http\Request;

class OAuthController extends Controller
{
    public function setToken(Request $request, $slug){
        $state = bin2hex(random_bytes(16));

        $client = AmoClient::where('slug', $slug)->firstOrFail()->getClient();

        $url = $client->getOAuthClient()->getAuthorizeUrl([
            'state' => $state,
            'mode' => 'post_message',
        ]);

        return redirect($url);
    }

    public function getToken(Request $request, $slug){
        $client = AmoClient::where('slug', $slug)->firstOrFail();

        $accessToken = $client->getClient()
            ->getOAuthClient()
            ->getAccessTokenByCode($request->get('code'));

        $client->fill([
            'access_token' => $accessToken->getToken(),
            'refresh_token' => $accessToken->getRefreshToken(),
            'expires' => $accessToken->getExpires(),
            'baseDomain' => $request->get('referer'),
        ])->save();
    }
}
