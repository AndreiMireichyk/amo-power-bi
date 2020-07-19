<?php

namespace App\Models\OldAmo;

use AmoCRM\Filters\LeadsFilter;
use App\Models\AmoCrm\AmoCrm;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class AmoOldLead extends Model
{
    protected $guarded = [];

    protected $casts = ['raw' => 'array'];

    /**
     * Обновление всех лидов из AmoCrm
     *
     * @return void
     */
    public static function sync()
    {
        //Очищаем таблицу
        static::query()->delete();

        static::saveRemoteList();
    }

    /**
     * Получаем полный список Лидов из AmoCrm
     *
     * @return Collection
     */
    public static function saveRemoteList()
    {
        $page = 1;

        $limit = 250;

        $client = AmoCrm::whereSlug('old_sanatoriums')->first()->client;

        $leads = $client->leads();

        $filter = (new LeadsFilter())->setLimit($limit);

        while ($page) {

            try {
                $filter->setPage($page);

                $currentPage = $leads->get($filter, ['contacts'])->toArray();

                array_map(function ($lead) {
                    static::updateOrCreate(['id' => $lead['id']], $lead);
                }, static::prepareRaw($currentPage));

                echo "Страница $page. Лидов = " . $page * $limit . "\n";

                $page = $page + 1;

            } catch (\Exception $e) {
                $page = false;
                echo $e->getMessage() . "\n";
            }

        }
    }


    /**
     * Приводим к нужному формату
     * @param $raw
     * @return array
     */
    public static function prepareRaw($raw)
    {
        $prepared = [];

        foreach ($raw as $key => $item) {
            $lead = [
                'id' => $item['id'],
                'amo_old_contact_id' => $item['contacts'][0]['id'] ?? 0,
                'name' => $item['name'],
                'price' => $item['price'],
                'responsible_user_id' => $item['responsible_user_id'],
                'group_id' => $item['group_id'],
                'status_id' => $item['status_id'],
                'pipeline_id' => $item['pipeline_id'],
                'loss_reason_id' => $item['loss_reason_id'],
                'source_id' => $item['source_id'],
                'created_by' => $item['created_by'],
                'updated_by' => $item['updated_by'],
                'created_at' => $item['created_at'],
                'updated_at' => $item['updated_at'],
                'closed_at' => $item['closed_at'],
                'closest_task_at' => $item['closest_task_at'],
                'is_deleted' => $item['is_deleted'],
                'score' => $item['score'],
                'account_id' => $item['account_id'],
                'raw' => $item,
            ];

            array_push($prepared, $lead);

        }

        return $prepared;
    }

    public function contact()
    {
        return $this->hasOne(AmoOldContact::class);
    }

    public function notes()
    {
    }

    public function tasks()
    {
    }
    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
}
