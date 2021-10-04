<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class CustomersExport implements FromCollection, WithHeadings
{
    protected $searchTerm;

    public function __construct($searchTerm)
    {
        $this->searchTerm = $searchTerm;
    }

    public function headings(): array
    {
        return [
            '#ID', 'Name', 'Contact Point', 'Phone', 'Tax No.', 'Debtor', 'creditor', 'corporate'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $customers = DB::table('customer')
        ->select('customer.customerid','customer.name','customer.contact1','customer.phone1'
                ,'customer.taxid','customer.debtor','customer.creditor','customer.corporate')
        ->whereNotNull('customerid')
        ->Where(function($query) {
                $query->where('customer.customerid', 'like', '%'.$this->searchTerm.'%')
                        ->orWhere('customer.name', 'like', '%'.$this->searchTerm.'%');
                })
        ->get();

        return $customers;
    }
}
