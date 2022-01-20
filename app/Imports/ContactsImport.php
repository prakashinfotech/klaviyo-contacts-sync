<?php

namespace App\Imports;

use App\Models\Contact;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Row;
use App\Jobs\SendContactJob;

class ContactsImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    /**
     * @param  WithHeadingRow|mixed  $importable
     * @return int
     */
    public function headingRow(): int
    {
        return 1;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function rules(): array
    {
        return [
            'name' => 'required|min:1|max:150',
            'email' => 'required|email|unique:contacts,email|min:1|max:150',
            'phone' => 'required|unique:contacts,phone|min:10|max:12'
        ];
    }

    public function model(array $row)
    {
        $contact =  Contact::firstOrCreate([
            'user_id' => auth()->user()->id,
            'name' => $row['name'],
            'email' => $row['email'],
            'phone' => $row['phone'],
        ]);

        dispatch(new SendContactJob($contact, auth()->user()->token));

        return $contact;
    }
}
