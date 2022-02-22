<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MatchController extends Controller
{
    /**
     * Get all search profiles that match this property
     *
     * @return \Illuminate\Http\Response
     */
    public function getMatchingPropertyProfiles(Request $request)
    {
        $res = (object) [];
        $res->data = [];
        return response()->json($res, 200);
    }
}
