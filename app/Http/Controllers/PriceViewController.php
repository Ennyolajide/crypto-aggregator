<?php

namespace App\Http\Controllers;

use App\Models\CryptoPrice;
use Illuminate\Support\Facades\Log;

class PriceViewController extends Controller
{
    public function index()
    {
        return view('prices.index');
    }
}