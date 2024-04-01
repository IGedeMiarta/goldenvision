<?php
namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;

class WithdrawExport implements FromQuery
{
    use Exportable;

    public function query()
    {
        return DB::table('withdrawals as w')
            ->join('users as u', 'w.user_id', '=', 'u.id')
            ->where('w.status', '=', 2)
            ->select(
                'u.username',
                DB::raw('CONCAT(u.firstname, " ", u.lastname) AS fullname'),
                'u.email',
                'u.email_dinaran',
                'u.mobile',
                'w.amount',
                DB::raw('CASE 
                    WHEN w.status = 0 THEN "Failed"
                    WHEN w.status = 1 THEN "Success"
                    WHEN w.status = 2 THEN "Pending"
                    WHEN w.status = 3 THEN "Cancel"
                    ELSE NULL
                END AS status'),
                'w.created_at'
            )
            ->orderBy('w.created_at', 'desc'); // Add orderBy clause here
    }
}
