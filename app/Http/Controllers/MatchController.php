<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\SearchProfile;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    /**
     * Get all search profiles that match this property
     *
     * @return \Illuminate\Http\Response
     */
    public function getMatchingPropertyProfiles(Request $request, Property $property)
    {
        $matchingProfiles = [];
        //get all search profiles that have the same property_type_id
        $searchProfiles = SearchProfile::with('searchProfileFields')
            ->where('property_type_id', $property->property_type_id)->get();

        foreach ($searchProfiles as $searchProfile) {
            //check if the search profile matches the property
            $matching =  $this->getMatchingStatus($searchProfile, $property);
            if ($matching->status) {
                //add the  search profile to the list of matching profiles
                $matchingProfiles[] = [
                    'searchProfileId' => $searchProfile->id,
                    'score' => $this->calculateScore($matching->looseMatchesCount, $matching->strictMatchesCount),
                    'strictMatchesCount' => $matching->strictMatchesCount,
                    'looseMatchesCount' => $matching->looseMatchesCount
                ];
            }
        }
        $res = (object) [];
        $res->data = $matchingProfiles;
        return response()->json($res, 200);
    }
    /**
     * Get all fields that appear in both the search profile fields and the property fields
     * @param $propertyFields
     * @param $profileFields
     * @return array profileFields that appear in the propertyFields
     */
    public function getSimilarFields($propertyFields, $profileFields)
    {

        // $intersect = $profileFields->intersect($propertyFields);
        //filter the profile fields to only include the fields that appear in the property fields
        $intersect = $profileFields->filter(function ($field) use ($propertyFields) {
            return in_array($field->name, $propertyFields->pluck('name')->toArray());
        });
        return $intersect;
    }

    /**
     * function to check if a search profile is a matching using intersecting fields
     * @param $propertyFields
     * @param $profile
     * @return object
     * */
    private function getMatchingStatus($searchProfile, $property)
    {
        $looseMatchesCount = 0;
        $strictMatchesCount = 0;
        $matching = false;
        //get all fields of the search profile and the property
        $searchProfileFields = $searchProfile->searchProfileFields;
        $propertyFields = $property->propertyFields;

        //get all fields that appear in both the search profile and the property
        $intersectingFields = $this->getSimilarFields($propertyFields, $searchProfileFields);
        //for each fied,
        foreach ($intersectingFields as $intersectingField) {
            //get the field value and type
            $propertyField = $propertyFields->firstWhere('name', $intersectingField->name);
            $propertyFieldValue = $propertyField->value;
            $searchProfileFieldType = $intersectingField->value_type;
            //if the field type is direct, check if the field value is equal to the property value
            if ($searchProfileFieldType == 'direct') {
                $searchProfileFieldValue = $intersectingField->min_value;
                //if the field value is not equal to the property value, their is a mismatch. discard the search profile
                if ($propertyFieldValue != $searchProfileFieldValue) {
                    $matching = false;
                    break;
                }
                $matching = true;
                $strictMatchesCount++;
            } else { //if the field type is range, check if the property value is within the range

                $minValue = $intersectingField->min_value;
                $maxValue = $intersectingField->max_value;
                //calculate the deviated range values
                $minDeviated = $minValue - ($minValue * 0.25);
                $maxDeviated = $maxValue + ($maxValue * 0.25);
                //if the property value is within the range, the search profile is a matching
                if ($propertyFieldValue >= $minValue && $propertyFieldValue <= $maxValue) {
                    //strict match
                    $matching = true;
                    $strictMatchesCount++;
                } else if ($propertyFieldValue >= $minDeviated && $propertyFieldValue <= $maxDeviated) {
                    //loose match
                    $matching = true;
                    $looseMatchesCount++;
                } else {
                    //miss match
                    $matching = false;
                    break;
                }
            }
        }
        //return the matching status, strict match count and the loose match count as an object
        $res = (object) [];
        $res->status = $matching;
        $res->strictMatchesCount = $strictMatchesCount;
        $res->looseMatchesCount = $looseMatchesCount;
        return $res;
    }
    //function to calculate weighted score of a search profile from the looseMatchesCount and strictMatchesCount
    private function calculateScore($looseMatchesCount, $strictMatchesCount)
    {
        $score = 0;
        if ($looseMatchesCount > 0) {
            $score = $score + ($looseMatchesCount * 0.5);
        }
        if ($strictMatchesCount > 0) {
            $score = $score + ($strictMatchesCount * 1);
        }
        return $score;
    }
}
