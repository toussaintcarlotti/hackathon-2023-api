<?php

namespace App\Http\Controllers;

use App\Actions\GetGbData;
use Illuminate\Support\Facades\Http;

class GbSectionController extends Controller
{
    public function __invoke()
    {
        return response()->json(GetGbData::run());
    }
}
