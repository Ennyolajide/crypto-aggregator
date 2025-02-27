<?php

use Illuminate\Support\Facades\Broadcast;


Broadcast::channel('price.{pair}', function ($user, $pair) {
    return in_array($pair, config('crypto.pairs', []));
});