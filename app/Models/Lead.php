<?php

namespace App\Models;

use AmoCRM\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Lead extends Model
{
    protected $guarded = [];


    /**
     * Обновление всех контактов из AmoCrm
     *
     * @return Collection
     */
    public static function sync()
    {
        //Получаем полный список лидов
        $remoteRaw = static::getRemoteList();

        //Приводим к нужному формату
        $preparedRaw = static::prepareRaw($remoteRaw);

        //Создаем лиды
       foreach ($preparedRaw as $raw){
           static::create($raw);
       }
    }


    /**
     * Получаем полный список Лидов из AmoCrm
     *
     * @param string $query
     * @return array|void
     */
    public static function getRemoteList($query = '')
    {
        $client = new Client(env('AMO_DOMAIN'), env('AMO_LOGIN'), env('AMO_HASH'));

        $fullList = [];

        $page = 1;

        while ($page) {
            $limit = 500;

            $offset = $limit * ($page - 1);

            $currentPage = $client->lead->apiList([
                'query' => $query,
                'limit_offset' => $offset,
                'limit_rows' => $limit
            ]);


            $fullList = array_merge($fullList, $currentPage);

            $page = count($currentPage) ? $page + 1 : false;

        }

        return $fullList;
    }

    public static function prepareRaw($raw)
    {
        $prepared = [];

        foreach ($raw as $item){
            $lead = [
                'id'=>$item['id'],
                'name'=>$item['name'],
                'date_create'=>$item['date_create'],
                'created_user_id'=>$item['created_user_id'],
                'last_modified'=>$item['last_modified'],
                'account_id'=>$item['account_id'],
                'price'=>$item['price'],
                'responsible_user_id'=>$item['responsible_user_id'],
                'linked_company_id'=>$item['linked_company_id'],
                'group_id'=>$item['group_id'],
                'pipeline_id'=>$item['pipeline_id'],
                'date_close'=>$item['date_close'],
                'closest_task'=>$item['closest_task'],
                'loss_reason_id'=>$item['loss_reason_id'],
                'modified_user_id'=>$item['modified_user_id'],
                'deleted'=>$item['deleted'],
                'status_id'=>$item['status_id'],
            ];


            foreach ($item['custom_fields'] as $field){
                $lead[$field['id']] =  implode(';',array_column($field['values'], 'value'));
            }

            array_push($prepared, $lead);

        }

        return $prepared;

    }

    /**
     * Пересоздаем таблицу для актуализации полей
     *
     */
    public static function tableColumnMapping()
    {
        $client = new Client(env('AMO_DOMAIN'), env('AMO_LOGIN'), env('AMO_HASH'));

        $custom_fields = collect($client->account->apiCurrent($short = false)['custom_fields']['leads']);

        $prepared_fields = $custom_fields->pluck('name', 'id');

        Schema::dropIfExists('leads');

        Schema::create('leads', function (Blueprint $table) use ($prepared_fields) {

            $table->engine = 'MyISAM';

            $table->bigIncrements('id');
            $table->integer('amo_new_contact_id');
            $table->text('name')->nullable();
            $table->string('date_create')->nullable();
            $table->string('created_user_id')->nullable();
            $table->string('last_modified')->nullable();
            $table->string('account_id')->nullable();
            $table->string('price')->nullable();
            $table->string('responsible_user_id')->nullable();
            $table->string('linked_company_id')->nullable();
            $table->string('group_id')->nullable();
            $table->string('pipeline_id')->nullable();
            $table->string('date_close')->nullable();
            $table->string('closest_task')->nullable();
            $table->string('loss_reason_id')->nullable();
            $table->string('modified_user_id')->nullable();
            $table->string('deleted')->nullable();
            $table->string('status_id')->nullable();

            //custom fields
            foreach ($prepared_fields as $key => $name) {
                $table->text("$key")->nullable()->comment($name);
            }

            $table->timestamps();


        });
    }

    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
}
