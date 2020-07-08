<?php

namespace App\Models\NewAmo;

use App\Models\AmoCrm\AmoCrm;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class AmoNewUser extends Model
{
    protected $guarded = [];

    /**
     * Синхронизация пользователей
     */
    public static function sync()
    {
        //Очищаем таблицы
        static::query()->delete();

        //Получаем список пользователей
        $remoteRaw = static::getRemoteList();

        //Создаем
        foreach ($remoteRaw as $raw) {
            static::create([
                'id' => $raw['id'],
                'name' => $raw['name'],
                'email' => $raw['email'],
            ]);
        }
    }

    /**
     * Получаем полный список пользователей
     *
     * @return Collection
     */
    public static function getRemoteList()
    {
        $client = AmoCrm::whereSlug('new_sanatoriums')->firstOrFail()->client;

        $users = $client->users();

        return collect($users->get(null, ['group'])->toArray());
    }
}
