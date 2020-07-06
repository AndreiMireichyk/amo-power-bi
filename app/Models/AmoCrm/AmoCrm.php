<?php

namespace App\Models\AmoCrm;

use Illuminate\Database\Eloquent\Model;

class AmoCrm extends Model
{
    protected $fillable = ['title', 'slug', 'client_id', 'secret', 'access_token', 'refresh_token', 'expires', 'base_domain'];

    use AmoCrmAuth;

    public $client;

    public static function boot()
    {
        parent::boot();

        static::retrieved(function ($model) {

            $model->setClient();

            if ($model->access_token) {
                $model->checkToken();
            }
        });
    }

}
