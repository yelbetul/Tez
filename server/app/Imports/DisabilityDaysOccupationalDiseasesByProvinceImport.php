<?php

namespace App\Imports;

use App\Models\DisabilityDaysOccupationalDiseasesByProvince;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;

class DisabilityDaysOccupationalDiseasesByProvinceImport implements ToCollection, WithHeadingRow
{
    public $failures = [];

    public function collection(Collection $rows)
    {
        $dataToInsert = [];

        foreach ($rows as $index => $row) {
            $validator = Validator::make($row->toArray(), [
                'year' => 'required',
                'province_id' => 'required|integer|exists:province_codes,id',
                'gender' => 'required|boolean',
                'outpatient' => 'required|integer',
                'inpatient' => 'required|integer',
            ]);

            if ($validator->fails()) {
                $this->failures[] = [
                    'row' => $index + 2, // heading row +1
                    'errors' => $validator->errors()->all()
                ];
            } else {
                $dataToInsert[] = [
                    'year' => $row['year'],
                    'province_id' => $row['province_id'],
                    'gender' => $row['gender'],
                    'outpatient' => $row['outpatient'],
                    'inpatient' => $row['inpatient'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Eğer hiç hata yoksa topluca veritabanına yaz
        if (empty($this->failures)) {
            DisabilityDaysOccupationalDiseasesByProvince::insert($dataToInsert);
        }
    }

    public function failures()
    {
        return $this->failures;
    }
}