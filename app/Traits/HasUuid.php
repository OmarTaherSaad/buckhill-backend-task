<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
    protected static function bootHasUuid(): void
    {
        static::creating(function ($model): void {
            //Check unique UUID
            $uuid = Str::uuid()->toString();
            while (static::where('uuid', $uuid)->count() > 0) {
                $uuid = Str::uuid()->toString();
            }
            $model->setAttribute('uuid', $uuid);
        });
    }
}
