<?php

namespace App\Imports;

use App\Models\AccidentsAndFatalitiesByInjuryCause;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;

class AccidentsAndFatalitiesByInjuryCauseImport implements ToCollection, WithHeadingRow
{
    public $failures = [];

    public function collection(Collection $rows)
    {
        $dataToInsert = [];

        foreach ($rows as $index => $row) {
            $validator = Validator::make($row->toArray(), [
                'year' => 'required',
                'group_id' => 'required|integer|exists:injury_causes,id',
                'gender' => 'required|boolean',
                'works_on_accident_day' => 'required|integer',
                'unfit_on_accident_day' => 'required|integer',
                'two_days_unfit' => 'required|integer',
                'three_days_unfit' => 'required|integer',
                'four_days_unfit' => 'required|integer',
                'five_or_more_days_unfit' => 'required|integer',
                'fatalities' => 'required|integer',
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
                    'works_on_accident_day' => $row['works_on_accident_day'],
                    'unfit_on_accident_day' => $row['unfit_on_accident_day'],
                    'two_days_unfit' => $row['two_days_unfit'],
                    'three_days_unfit' => $row['three_days_unfit'],
                    'four_days_unfit' => $row['four_days_unfit'],
                    'five_or_more_days_unfit' => $row['five_or_more_days_unfit'],
                    'fatalities' => $row['fatalities'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Eğer hiç hata yoksa topluca veritabanına yaz
        if (empty($this->failures)) {
            AccidentsAndFatalitiesByInjuryCause::insert($dataToInsert);
        }
    }

    public function failures()
    {
        return $this->failures;
    }
}