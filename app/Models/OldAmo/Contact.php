<?php

namespace App\Models\OldAmo;

use AmoCRM\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Contact extends Model
{
    protected $guarded = [];

    protected $casts = ['raw' => 'array'];



    /**
     * Обновление всех контактов из AmoCrm
     *
     * @return Collection
     */
    public static function sync()
    {
        //Получаем полный список контактов
        $remoteRaw = static::getRemoteList();

        //Удаляем контакты которых уже нет в амо
        static::whereNotIn('id', collect($remoteRaw)->pluck('id')->toArray())->delete();

        //Создаем или обновляем локальный контакт
        return static::manyUpdateOrCreateFromRaw($remoteRaw);
    }

    /**
     * Создает или обновляет контакты из запрошенных данных AmoCrm
     *
     * @param $raw
     * @return Collection
     */
    public static function manyUpdateOrCreateFromRaw($raw)
    {
        $contacts = collect();

        $amo_fields = config('amocrm_fields.'.env('APP_ENV'));

        foreach ($raw as $contact) {

            $fields = collect($contact['custom_fields']);

            $phones = $fields->where('id', $amo_fields['phone'])->first()['values'];
            $phone = collect($phones)->first()['value'];

            $email = $fields->where('id', $amo_fields['email'])->first()['values'][0]['value'] ?? '';

            $prepared_phone = str_replace(['+', '(', ')', '-', ' '], '', $phone);
            $prepared_email = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;


            //Добавляем или обновляем контакт
            $__contact = static::updateOrCreate(
                ['id' => $contact['id']],
                [
                    'phone' => $prepared_phone,
                    'email' => $prepared_email,
                    'raw' => $contact,
                    'responsible_user_id' => $contact['responsible_user_id'] ?? null
                ]
            );

            $contacts->push($__contact);
        }


        return $contacts;
    }

    /**
     * Получаем полный список контактов из AmoCrm
     *
     * @param string $query
     * @return array|void
     */
    public static function getRemoteList($query = '')
    {
        $client = new Client(env('OLD_AMO_DOMAIN'), env('OLD_AMO_LOGIN'), env('OLD_AMO_HASH'));

        $fullList = [];

        $page = 1;

        while ($page) {
            $limit = 500;

            $offset = $limit * ($page - 1);

            $currentPage = $client->contact->apiList([
                'query' => $query,
                'limit_offset' => $offset,
                'limit_rows' => $limit
            ]);


            $fullList = array_merge($fullList, $currentPage);

            $page = count($currentPage) ? $page + 1 : false;

        }

        return $fullList;
    }
}
