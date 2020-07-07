<?php

namespace App\Http\Controllers\Amo;

use App\Http\Controllers\Controller;
use App\Models\AmoCrm\AmoCrm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class OAuthController extends Controller
{

    public function index(){
        return view('welcome', [
            'accounts'=>AmoCrm::all()
        ]);
    }
    /**
     * Запрос токена
     *
     * @param Request $request
     * @param $slug
     * @return RedirectResponse|Redirector
     * @throws \Exception
     */
    public function requestToken(Request $request, $slug){
        $state = bin2hex(random_bytes(16));

        $amo = AmoCrm::where('slug', $slug)->firstOrFail();

        $url = $amo->client->getOAuthClient()->getAuthorizeUrl([
            'state' => $state,
            'mode' => 'post_message',
        ]);

        return redirect($url);
    }

    /**
     * WebHook получение запрошенного токена
     *
     * @param Request $request
     * @param $slug
     * @return RedirectResponse
     */
    public function setToken(Request $request, $slug){
        $amo = AmoCrm::where('slug', $slug)->firstOrFail();

        $accessToken = $amo->client
            ->setAccountBaseDomain($amo->getAttribute('base_domain'))
            ->getOAuthClient()
            ->getAccessTokenByCode($request->get('code'));


        $amo->fill([
            'access_token' => $accessToken->getToken(),
            'refresh_token' => $accessToken->getRefreshToken(),
            'expires' => $accessToken->getExpires(),
            'baseDomain' => $request->get('referer'),
        ])->save();

        return redirect()->route('home');
    }

    public function test()
    {
        $amo = AmoCrm::whereSlug('old_sanatoriums')->firstOrFail();
        dd( $contact = $amo->client->contacts()->get());
        dd($amo->client);

    }
}
