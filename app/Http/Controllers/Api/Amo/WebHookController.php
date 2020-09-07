<?php

namespace App\Http\Controllers\Api\Amo;

use App\Http\Controllers\Controller;
use App\Models\NewAmo\AmoNewLead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WebHookController extends Controller
{
    public function handler(Request $request)
    {
        Storage::disk('local')->put('amocrm_webhook.txt', var_export($request->all(), true));

        $leads = $request->get('leads');

        $contacts = $request->get('contacts');


      /*  foreach ($leads['update'] ?? [] as $lead){
            AmoNewLead::syncById($lead['id']);
        }

        foreach ($leads['add'] ?? [] as $lead){
            AmoNewLead::syncById($lead['id']);
        }

        foreach ($leads['delete'] ?? [] as $lead){
            AmoNewLead::where('id', $lead['id'])->delete();
        }*/
    }
}
