<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;

// Models
use App\Models\Person;
use App\Models\Country;
use App\Models\State;
use App\Models\City;

class PeopleImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if (strtolower($row[0]) != 'nombre completo') {
            $country_id = $row[8] ? Country::firstOrCreate(['name' => ucfirst(strtolower($row[8]))])->id : 1;
            $state_id = $row[7] ? State::firstOrCreate(['country_id' => $country_id, 'name' => ucfirst(strtolower($row[7]))])->id : 1;
            $city_id = $row[6] ? City::firstOrCreate(['state_id' => $state_id, 'name' => ucfirst(strtolower($row[6]))])->id : 1;

            return new Person([
                'full_name' => $row[0],
                'dni' => $row[1],
                'phone' => $row[3],
                'address' => $row[4],
                'birthday' => $row[2],
                'city_id' => $city_id,
                'job' => $row[5]
            ]);
        }
    }
}
