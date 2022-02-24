

## About the app

Matcher is a simple microservice build using laravel, which matches a given real estate property to a list of search profiles.  
It contains a single Get endpoint which accepts a property id and return a list of search profiles that match the property.

Request: `{baseUrl}/api/match/{propertyId}`  
Response: 

        "data" => [
            ["searchProfileId" => {id}, "score" => {matchScore}, "strictMatchesCount" => {counter},"looseMatchesCount" => {counter}],
            ["searchProfileId" => {id}, "score" => {matchScore}, "strictMatchesCount" => {counter},"looseMatchesCount" => {counter}],
            ["searchProfileId" => {id}, "score" => {matchScore}, "strictMatchesCount" => {counter},"looseMatchesCount" => {counter}],
            [...],
            [...],
            [...]
            ]

Sample request: `{baseUrl}/api/match/1` 

Sample response: 

            "data" => [
                [
                    "searchProfileId" => 4,
                    "score" => 1.5,
                    "strictMatchesCount" => 1,
                    "looseMatchesCount" => 1
                ]
            ]

## Data structures
The application has mainly five models:
1. Property
2. SearchProfile
3. PropertyFields
4. SearchProfileField
5. PropertyType

### Property
The property class represents the real estate property. has the following attributes:
- name
- address
- property_type_id - the property type id of the property

It has a one to many relationship with `PropertyFields`.
### PropertyFields
The property fields class which represents the fields of the real estate property. has the following attributes:
- name - name of the field ie price, area, rooms, etc
- value - the value of the property field ie 50000 for price field

It has a one to one relationship with `Property`.
### SearchProfile
The search profile class which represents the search profile. has the following attributes:
- name
- property_type_id - the property type id of the search profile

It has a one to many relationship with `SearchProfileField`.
### SearchProfileField
The search profile fields class which represents the fields of the search profile. has the following attributes:
- name - name of the field ie price, rooms, area etc
- min_value - represents the minimum value of the field if it is a range field, and the value of the field if it is a direct field.
- max_value - represents the maximum value of the field if it is a range field, and null if it is a direct field.
- value_type - The type of value stored. ['direct','range']

It has a one to one relationship with `SearchProfile`.
### PropertyType
The property type class which represents the type of the real estate property. has the following attributes:
- name
- description - The description of the property type.

It has a one to many relationship with `Property`.


