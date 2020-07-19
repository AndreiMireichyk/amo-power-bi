<?php

namespace App\Models\OldAmo;

use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Helpers\EntityTypesInterface;
use App\Models\AmoCrm\AmoCrm;
use Illuminate\Database\Eloquent\Model;

class AmoOldTask extends Model
{
    protected $guarded = [];

    protected $casts = [ 'is_completed'=>'boolean'];

    /**
     * Обновление задач
     *
     * @return void
     */
    public static function sync()
    {
       // static::query()->delete();

        static::saveRemoteList();
    }


    public static function saveRemoteList()
    {
        $page = 1;

        $limit = 250;

        $client = AmoCrm::whereSlug('old_sanatoriums')->first()->client;

        $tasks = $client->tasks(EntityTypesInterface::TASKS);

        $filter = (new LeadsFilter())->setLimit($limit);

        while ($page) {

            try {
                $filter->setPage($page);

                $currentPage = $tasks->get($filter, ['contacts'])->toArray();

                array_map(function ($tasks) {
                    static::updateOrCreate(['id' => $tasks['id']], $tasks);
                }, $currentPage);

                echo "Страница $page. Лидов = " . $page * $limit . "\n";

                $page = $page + 1;

            } catch (\Exception $e) {
                $page = false;
                echo $e->getMessage() . "\n";
            }

        }
    }

}
