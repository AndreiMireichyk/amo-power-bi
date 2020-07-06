<?php

namespace App\Models\OldAmo;

use AmoCRM\Client;
use AmoCRM\Filters\ContactsFilter;
use App\Models\AmoCrm\AmoCrm;
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
        static::query()->delete();

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

        foreach ($raw as $key => $contact) {

            echo "Контакт - " . $key . "\n";

            $fields = collect($contact['custom_fields_values']);

            $phones = $fields->where('field_code', 'PHONE')->first()['values'];

            $phone = collect($phones)->first()['value'];

            $prepared_phone = str_replace(['+', '(', ')', '-', ' '], '', $phone);

            $email = $fields->where('field_code', 'EMAIL')->first()['values'][0]['value'] ?? '';

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
     *
     * @return Collection
     */
    public static function getRemoteList()
    {
        $fullList = collect();
        $page = 1;
        $limit = 250;

        $client = AmoCrm::whereSlug('old_sanatoriums')->firstOrFail()->client;

        $contacts = $client->contacts();

        $filter = (new ContactsFilter())->setLimit($limit);

        while ($page) {
            echo "Страница $page. Контактов = " . count($fullList)*$limit . "\n";

            $filter->setPage($page);

            try {
                $currentPage = $contacts->get($filter);
                $fullList->push($currentPage->toArray());
                $page = $page + 1;
            } catch (\Exception $e) {
                $page = false;
                echo $e->getMessage()."\n";
            }
        }

        return $fullList->collapse();
    }
}
