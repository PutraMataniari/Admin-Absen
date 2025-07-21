<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/db-test', function () {
    try{
        DB::connection()->getPdo();
        return 'connection to DB: '.DB::connection()->getDatabaseName();
    } catch (\Exception $e) {
        return 'Connection failed: '. $e->getMessage();
    }
});
