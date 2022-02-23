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

    public function test_match_endpoint_returns_data_in_valid_format()
    {
        $propertyType  =  PropertyType::factory()
            ->create();

        $properties = Property::factory()
            ->count(3)
            ->state(['property_type_id' => $propertyType->id])
            ->has(PropertyField::factory()->count(5))
            ->create();

        $searchProfile = SearchProfile::factory()
            ->count(3)
            ->state(['property_type_id' => $propertyType->id])
            ->has(SearchProfileField::factory()->count(5))
            ->create();

        $response = $this->get("/api/match/{$properties->first()->id}");
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

    public function test_amatch_profile_is_found_for_a_property()
    {
        $property = Property::factory()->state([
            "property_type_id" => "d44d0090-a2b5-47f7-80bb-d6e6f85fca90",
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
            "id" => "9000",
            "property_type_id" => "d44d0090-a2b5-47f7-80bb-d6e6f85fca90",

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
                    "searchProfileId" => 9000,
                    "score" => 1,
                    "strictMatchesCount" => 1,
                    "looseMatchesCount" => 0
                ]
            ]
        ]);
    }
}
