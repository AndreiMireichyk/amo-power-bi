<?php

namespace App\Models\NewAmo;

use AmoCRM\Models\AccountModel;
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
                'group_name' => $raw['group_name'],
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

        $account = $client->account()->getCurrent(AccountModel::getAvailableWith());

        $groups = $account->getUsersGroups();

        $users = $client->users();

        $users = $users->get(null, ['groups']);

        return collect($users->toArray())->map(function ($user) use ($groups) {
            $group_id = $user['rights']['group_id'] ?? 0;
            $user['group_name'] = $groups->getBy('id', $group_id)->name;
            return $user;
        });
    }
}
