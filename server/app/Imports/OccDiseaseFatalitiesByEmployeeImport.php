<?php

namespace App\Imports;

use App\Models\OccDiseaseFatalitiesByEmployee;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;

class OccDiseaseFatalitiesByEmployeeImport implements ToCollection, WithHeadingRow
{
    public $failures = [];

    public function collection(Collection $rows)
    {
        $dataToInsert = [];

        foreach ($rows as $index => $row) {
            $validator = Validator::make($row->toArray(), [
                'year' => 'required',
                'group_id' => 'required|integer|exists:employee_groups,id',
                'gender' => 'required|boolean',
                'occ_disease_cases' => 'required|integer',
                'occ_disease_fatalities' => 'required|integer',
            ]);

            if ($validator->fails()) {
                $this->failures[] = [
                    'row' => $index + 2, // heading row +1
                    'errors' => $validator->errors()->all()
                ];
            } else {
                $dataToInsert[] = [
                    'year' => $row['year'],
                    'group_id' => $row['group_id'],
                    'gender' => $row['gender'],
                    'occ_disease_cases' => $row['occ_disease_cases'],
                    'occ_disease_fatalities' => $row['occ_disease_fatalities'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Eğer hiç hata yoksa topluca veritabanına yaz
        if (empty($this->failures)) {
            OccDiseaseFatalitiesByEmployee::insert($dataToInsert);
        }
    }

    public function failures()
    {
        return $this->failures;
    }
}