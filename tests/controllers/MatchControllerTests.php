<?php

namespace Tests\Controllers;

use Illuminate\Http\Response;
use Tests\TestCase;

class MatchControllerTests extends TestCase {
    public function test_match_endpoint_returns_data_in_valid_format(){
        $property_id = 1;
        $response = $this->get("/api/match/$property_id");
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            "data" => [
               '*' => [
                    "searchProfileId",
                    "score",
                    "strictMatchesCount",
                    "looseMatchesCount"
                ]
            ]
        ]);

    }
}