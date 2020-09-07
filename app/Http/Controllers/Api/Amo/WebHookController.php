<?php

namespace App\Http\Controllers\Api\Amo;

use App\Http\Controllers\Controller;
use App\Models\NewAmo\AmoNewContact;
use App\Models\NewAmo\AmoNewLead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WebHookController extends Controller
{
    public function handler(Request $request)
    {
        Storage::disk('local')->put('amocrm_webhook.txt', var_export($request->all(), true));

        $leads = $request->get('leads') ?? [];

        $contacts = $request->get('contacts') ?? [];

        $unsorted = $request->get('unsorted') ?? [];

        foreach ($leads['update'] ?? [] as $lead){
            Storage::disk('local')->put('amocrm_webhook_lead_update.txt', var_export($request->all(), true));
            AmoNewLead::syncById($lead['id']);
        }

        foreach ($leads['add'] ?? [] as $lead){
            Storage::disk('local')->put('amocrm_webhook_lead_add.txt', var_export($request->all(), true));
            AmoNewLead::syncById($lead['id']);
        }

        foreach ($leads['delete'] ?? [] as $lead){
            Storage::disk('local')->put('amocrm_webhook_lead_delete.txt', var_export($request->all(), true));
           AmoNewLead::where('id', $lead['id'])->delete();
        }



        foreach ($contacts['update'] ?? [] as $contact){
            Storage::disk('local')->put('amocrm_webhook_contact_update.txt', var_export($request->all(), true));
           AmoNewContact::syncById($contact['id']);
        }

        foreach ($contacts['add'] ?? [] as $contact){
            Storage::disk('local')->put('amocrm_webhook_contact_add.txt', var_export($request->all(), true));
            AmoNewContact::syncById($contact['id']);
        }

        foreach ($contacts['delete'] ?? [] as $contact){
            Storage::disk('local')->put('amocrm_webhook_contact_delete.txt', var_export($request->all(), true));
           AmoNewContact::where('id', $contact['id'])->delete();
        }


        foreach ($unsorted['add'] ?? [] as $lead){
            Storage::disk('local')->put('amocrm_webhook_unsorted_add.txt', var_export($request->all(), true));
            //AmoNewLead::syncById($lead['id']);
        }

        foreach ($unsorted['delete'] ?? [] as $lead){
            Storage::disk('local')->put('amocrm_webhook_unsorted_delete.txt', var_export($request->all(), true));
            //AmoNewLead::syncById($lead['id']);
        }
    }
}
