<?php

namespace App\Models\NewAmo;

use AmoCRM\Filters\CustomersFilter;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Helpers\EntityTypesInterface;
use App\Models\AmoCrm\AmoCrm;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class AmoNewLead extends Model
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
        //Получаем полный список лидов
        $remoteRaw = static::getRemoteList();

        //Приводим к нужному формату
        $preparedRaw = static::prepareRaw($remoteRaw);

       //Создаем лиды
       foreach ($preparedRaw as $raw){
           static::updateOrCreate(['id'=> $raw['id']] , $raw);
       }
    }

    /**
     * Получаем полный список Лидов из AmoCrm
     *
     * @return Collection
     */
    public static function getRemoteList()
    {
        $fullList = collect();
        $page = 1;
        $limit = 50;

        $client = AmoCrm::whereSlug('new_sanatoriums')->first()->client;

        $leads = $client->leads();

        $filter = (new LeadsFilter())->setLimit($limit);

        while ($page) {

            echo "Страница $page. Лидов = " . count($fullList)*$limit . "\n";

            $filter->setPage($page);

            try {
                $currentPage = $leads->get($filter, ['contacts']);
                $fullList->push($currentPage->toArray());
                $page = $page + 1;
            } catch (\Exception $e) {
                $page = false;
                echo $e->getMessage()."\n";
            }
        }

        return $fullList->collapse();
    }

    /**
     * Пересоздаем таблицу для актуализации полей
     *
     */
    public static function tableColumnMapping()
    {

        $amo = AmoCrm::whereSlug('new_sanatoriums')->first();

        $filter = (new CustomersFilter())->setLimit(250);

        $custom_fields = $amo->client->customFields(EntityTypesInterface::LEADS)->get($filter)->toArray();

        $prepared_fields = collect($custom_fields)->pluck('name', 'id');

        Schema::dropIfExists('amo_new_leads');

        Schema::create('amo_new_leads', function (Blueprint $table) use ($prepared_fields) {

            $table->engine = 'MyISAM';

            $table->bigIncrements('id');
            $table->integer('amo_new_contact_id');
            $table->text('name')->nullable();
            $table->text('price')->nullable();
            $table->text('responsible_user_id')->nullable();
            $table->text('group_id')->nullable();
            $table->text('status_id')->nullable();
            $table->text('pipeline_id')->nullable();
            $table->text('loss_reason_id')->nullable();
            $table->text('source_id')->nullable();
            $table->text('created_by')->nullable();
            $table->text('updated_by')->nullable();
            $table->text('closest_task_at')->nullable();
            $table->text('is_deleted')->nullable();
            $table->text('score')->nullable();
            $table->text('account_id')->nullable();

            //custom fields
            foreach ($prepared_fields as $key => $name) {
                $table->text("$key")->nullable()->comment($name);
            }

            $table->text('created_at')->nullable();
            $table->text('updated_at')->nullable();
            $table->text('closed_at')->nullable();
        });
    }

    /**
     * Приводим к нужному формату
     *
     */
    public static function prepareRaw($raw)
    {
        $prepared = [];

        foreach ($raw as $item){

            $lead = [
                'id'=>$item['id'],
                'amo_new_contact_id'=>$item['contacts'][0]['id'] ?? 0,
                'name'=>$item['name'],
                'price'=>$item['price'],
                'responsible_user_id'=>$item['responsible_user_id'],
                'group_id'=>$item['group_id'],
                'status_id'=>$item['status_id'],
                'pipeline_id'=>$item['pipeline_id'],
                'loss_reason_id'=>$item['loss_reason_id'],
                'source_id'=>$item['source_id'],
                'created_by'=>$item['created_by'],
                'updated_by'=>$item['updated_by'],
                'created_at'=>$item['created_at'],
                'updated_at'=>$item['updated_at'],
                'closed_at'=>$item['closed_at'],
                'closest_task_at'=>$item['closest_task_at'],
                'is_deleted'=>$item['is_deleted'],
                'score'=>$item['score'],
                'account_id'=>$item['account_id'],
            ];

            foreach ($item['custom_fields_values']->toArray() as $field){
                $lead[$field['field_id']] =  implode(';',array_column($field['values'], 'value'));
            }

            array_push($prepared, $lead);

        }

        return $prepared;

    }
}
