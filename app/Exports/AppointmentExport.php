<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class AppointmentExport implements FromCollection, WithHeadings, WithMapping
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
        $name = $record->customer ? $record->customer->full_name : '';
        $appointmentId = $record->appointmentId;
        $phoneno = $record->customer ? $record->customer->phone_number : '';
        $status = $record->status;
        $time = $record->time;
        $date = $record->date;
        return [
            $name,
            $appointmentId,
            $phoneno,
            $status,
            $time,
            $date,
        ];
    }


    public function headings(): array
    {
        return array(
            'Name',
            'Appointment ID',
            'Phone Number',
            'Status',
            'Time',
            'Date'
        );
    }
}
