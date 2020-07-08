<?php

namespace App\Models\NewAmo;

use App\Models\AmoCrm\AmoCrm;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class AmoNewPipeline extends Model
{
    protected $guarded = [];

    protected $casts = ['raw' => 'array'];

    /**
     * Обновление всех воронок из AmoCrm
     *
     * @return void
     */
    public static function sync()
    {
        static::query()->delete();

        $remoteRaw = static::getRemoteList();

        foreach ($remoteRaw as $raw){
            static::create([
                'id'=>$raw['id'],
                'name'=>$raw['name']
            ]);
        }
    }


    /**
     * Получаем полный список воронок из AmoCrm
     *
     *
     * @return Collection
     */
    public static function getRemoteList()
    {
        $client = AmoCrm::whereSlug('new_sanatoriums')->firstOrFail()->client;

        $pipelines = $client->pipelines();

        return  collect($pipelines->get()->toArray());
    }
}
