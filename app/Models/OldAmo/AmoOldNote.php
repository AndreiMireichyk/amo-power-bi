<?php

namespace App\Models\OldAmo;

use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Filters\NotesFilter;
use AmoCRM\Helpers\EntityTypesInterface;
use App\Models\AmoCrm\AmoCrm;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class AmoOldNote extends Model
{
    protected $guarded = [];

    protected $casts = ['params' => 'array'];

    /**
     * Обновление заметок
     *
     * @return void
     */
    public static function sync()
    {
        static::query()->delete();

        static::saveRemoteList();
    }

    /**
     * Получаем  список
     *
     * @return void
     */
    public static function saveRemoteList()
    {
        $page = 1;

        $limit = 125;

        $client = AmoCrm::whereSlug('old_sanatoriums')->first()->client;

        $notes = $client->notes(EntityTypesInterface::LEADS);

        $filter = (new NotesFilter())->setLimit($limit);

        while ($page) {

            try {
                $filter->setPage($page);

                $currentPage = $notes->get($filter, ['contacts'])->toArray();

                array_map(function ($note) {
                    static::updateOrCreate(['id' => $note['id']], $note);
                }, $currentPage);

                echo "Страница $page. Заметок = " . $page * $limit . "\n";

                $page = $page + 1;

            } catch (\Exception $e) {
                $page = false;
                echo $e->getMessage() . "\n";
            }

        }
    }

    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

}
