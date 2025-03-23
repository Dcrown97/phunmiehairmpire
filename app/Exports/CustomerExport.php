<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class CustomerExport implements FromCollection, WithHeadings, WithMapping
{
    protected $record;

    public function __construct($record)
    {
        $this->record = $record;
    }


    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $record = $this->record;
    }

    public function map($record): array
    {
        $name = $record->full_name;
        $email = $record->email;
        $phoneno = $record->phone_number;
        $lastVisit = $record->last_visit;

        return [
            $name,
            $email,
            $phoneno,
            $lastVisit,
        ];
    }


    public function headings(): array
    {
        return array(
            'Full Name',
            'Email Address',
            'Phone Number',
            'Last Visit Date'
        );
    }
}
