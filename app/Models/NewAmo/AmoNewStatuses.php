<?php

namespace App\Models\NewAmo;

use App\Models\AmoCrm\AmoCrm;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class AmoNewStatuses extends Model
{

    protected $guarded = [];

    public static function sync()
    {
        //Получаем полный список статусов
        $remoteRaw = static::getRemoteList();

        //Очищаем таблицу
        static::query()->delete();

        //Создаем
        foreach ($remoteRaw as $raw){
            static::create([
                'id'=>$raw['id'],
                'name'=>$raw['name'],
                'amo_new_pipeline_id'=>$raw['pipeline_id'],
            ]);
        }

    }

    /**
     * получаем полный список статусов воронок
     *
     * @return Collection
     */
    public static function getRemoteList()
    {
        $full_list = collect();

        $client = AmoCrm::whereSlug('new_sanatoriums')->firstOrFail()->client;

        foreach (AmoNewPipeline::all() as $pipeline){
            $statuses = $client->statuses($pipeline->id);
            $full_list->push($statuses->get()->toArray());
        }

        return $full_list->collapse();
    }
}
