<?php

namespace App\Imports;

use App\Models\Shop;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ShopsImport implements ToModel, WithValidation, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Shop([
            'name'     => $row['name'],
            'address'  => $row['address'],
            'email'    => $row['email'], 
        ]);
    }
    public function rules(): array
    {
        return [
            '*.name' => 'required',
        ];
    }

    /**
     * @return array
     */
    public function customValidationAttributes() {
        return [];
    }
}
