<?php

namespace Tests\Controllers;

use App\Http\Controllers\MatchController;
use App\Models\Property;
use App\Models\PropertyField;
use App\Models\PropertyType;
use App\Models\SearchProfile;
use App\Models\SearchProfileField;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class MatchControllerTest extends TestCase
{
    use RefreshDatabase;



    public function test_amatch_profile_is_found_for_a_property()
    {
        $property = Property::factory()->state([
            "property_type_id" => "d44d0090-a2b5-47f7-80bb-d6e6f85fca92",
        ])->create();
        $propertyFields = collect(
            [
                ["name" => "area", "value" => "180"],
                ["name" => "rooms", "value" => "5"],
                ["name" => "heatingType", "value" => "gas"],
                ["name" => "price", "value" => "120000"]
            ]
        );
        //loop through the property fields and create a propertyField object using factory
        $propertyFields->each(function ($field) use ($property) {
            PropertyField::factory()->state(['property_id' => $property->id])->create($field);
        });
        //create search profile
        $searchProfile = SearchProfile::factory()->state([
            "property_type_id" => "d44d0090-a2b5-47f7-80bb-d6e6f85fca92",

        ])->create();

        SearchProfileField::factory()->state([
            "search_profile_id" => $searchProfile->id,
            "name" => "price",
            "min_value" => 0,
            "max_value" => "200000",
            "value_type" => "range",
        ])->create();
        $property = Property::with('propertyFields')->find($property->id);
        $searchProfile = SearchProfile::with('searchProfileFields')->find($searchProfile->id);
        $response = $this->get("/api/match/{$property->id}");
        $response->assertStatus(Response::HTTP_OK);
        $response->assertExactJson([
            "data" => [
                [
                    "searchProfileId" => $searchProfile->id,
                    "score" => 1,
                    "strictMatchesCount" => 1,
                    "looseMatchesCount" => 0
                ]
            ]
        ]);
    }
    public function test_amismatch_profile_field_for_a_property()
    {
        $property = Property::factory()->state([
            "property_type_id" => "d44d0090-a2b5-47f7-80bb-d6e6f85fca93",
        ])->create();
        $propertyFields = collect(
            [
                ["name" => "area", "value" => "180"],
                ["name" => "rooms", "value" => "5"],
                ["name" => "heatingType", "value" => "gas"],
                ["name" => "price", "value" => "120000"]
            ]
        );
        $propertyFields->each(function ($field) use ($property) {
            PropertyField::factory()->state(['property_id' => $property->id])->create($field);
        });
        //create search profile
        $searchProfile = SearchProfile::factory()->state([
            "property_type_id" => "d44d0090-a2b5-47f7-80bb-d6e6f85fca93",

        ])->create();
        $profileFields = collect(
            [
                [
                    "search_profile_id" => $searchProfile->id,
                    "name" => "price",
                    "min_value" => 0,
                    "max_value" => 150000,
                    "value_type" => "range"
                ],
                [
                    "search_profile_id" => $searchProfile->id,
                    "name" => "rooms",
                    "min_value" => "3",
                    "max_value" => NULL,
                    "value_type" => "direct"
                ]
            ]
        );
        //loop through the property fields and create a propertyField object using factory
        $profileFields->each(function ($field) {
            SearchProfileField::factory()->create($field);
        });
        $property = Property::with('propertyFields')->find($property->id);
        $searchProfile = SearchProfile::with('searchProfileFields')->find($searchProfile->id);
        $response = $this->get("/api/match/{$property->id}");
        $response->assertStatus(Response::HTTP_OK);
        $response->assertExactJson([
            "data" => []
        ]);
    }

        public function test_match_endpoint_returns_data_in_valid_format()
    {
        $propertyType  =  PropertyType::factory()
            ->create();

        $property = Property::factory()->state([
            "property_type_id" => "d44d0090-a2b5-47f7-80bb-d6e6f85fca91",
        ])->create();
        $propertyFields = collect(
            [
                ["name" => "area", "value" => "180"],
                ["name" => "rooms", "value" => "5"],
                ["name" => "heatingType", "value" => "gas"],
                ["name" => "price", "value" => 120000]
            ]
        );
        $propertyFields->each(function ($field) use ($property) {
            PropertyField::factory()->state(['property_id' => $property->id])->create($field);
        });
        //create search profile
        $searchProfile = SearchProfile::factory()->state([
            "property_type_id" => "d44d0090-a2b5-47f7-80bb-d6e6f85fca91",
        ])->create();
        $profileFields = collect(
            [
                [
                    "search_profile_id" => $searchProfile->id,
                    "name" => "rooms",
                    "min_value" => "5",
                    "max_value" => NULL,
                    "value_type" => "direct"
                ]
            ]
        );
        $profileFields->each(function ($field) {
            SearchProfileField::factory()->create($field);
        });
        $response = $this->get("/api/match/{$property->id}");
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
    public function test_adirect_field_property_match()
    {
        $property1 = Property::factory()->state([
            "property_type_id" => "d44d0090-a2b5-47f7-80bb-d6e6f85fca94",
        ])->create();
        $propertyFields1 = collect(
            [
                ["name" => "area", "value" => "180"],
                ["name" => "rooms", "value" => "5"],
                ["name" => "heatingType", "value" => "gas"],
                ["name" => "price", "value" => 230000]
            ]
        );
        $propertyFields1->each(function ($field) use ($property1) {
            PropertyField::factory()->state(['property_id' => $property1->id])->create($field);
        });
        //create search profile
        $searchProfile1 = SearchProfile::factory()->state([
            "property_type_id" => "d44d0090-a2b5-47f7-80bb-d6e6f85fca94",
        ])->create();
        $profileFields1 = collect(
            [
                [
                    "search_profile_id" => $searchProfile1->id,
                    "name" => "rooms",
                    "min_value" => "5",
                    "max_value" => NULL,
                    "value_type" => "direct"
                ],
                [
                    "search_profile_id" => $searchProfile1->id,
                    "name" => "price",
                    "min_value" => "100000",
                    "max_value" => "200000",
                    "value_type" => "range"
                ]
            ]
        );
        //loop through the property fields and create a propertyField object using factory
        $profileFields1->each(function ($field) {
            SearchProfileField::factory()->create($field);
        });
        $property1 = Property::with('propertyFields')->find($property1->id);
        $searchProfile1 = SearchProfile::with('searchProfileFields')->find($searchProfile1->id);
        $response = $this->get("/api/match/{$property1->id}");
        $response->assertStatus(Response::HTTP_OK);
        $response->assertExactJson([
            "data" => [
                [
                    "searchProfileId" => $searchProfile1->id,
                    "score" => 1.5,
                    "strictMatchesCount" => 1,
                    "looseMatchesCount" => 1
                ]
            ]
        ]);
    }
}
