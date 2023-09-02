<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
    protected static function bootHasUuid()
    {
        static::creating(function ($model) {
            //Check unique UUID
            $uuid = Str::uuid()->toString();
            while (static::where('uuid', $uuid)->count() > 0) {
                $uuid = Str::uuid()->toString();
            }
            $model->setAttribute('uuid', $uuid);
        });
    }
}
