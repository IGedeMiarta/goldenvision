<?php

namespace App\Exports;

use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Illuminate\Support\Facades\DB;

class ExptUserQuery implements FromQuery
{
    /**
    * @return \Illuminate\Database\Query\Builder
    */
    use Exportable;

    public function __construct($search,$date)
    {

        $this->search = $search;
        $this->date = $date;
    }
    public function query()
    {
        
    $users = User::where('comp', 0)->select(
        DB::raw("CONCAT(firstname, ' ', lastname) AS fullname"),
        'username',
        'email',
        'email_dinaran',
        'pin',
        DB::raw("CASE WHEN comp = 1 THEN 'Management' WHEN comp = 0 THEN 'User' ELSE NULL END AS TypeUser"),
        DB::raw("CASE WHEN status = 1 THEN 'Active' WHEN status = 0 THEN 'Banned' ELSE NULL END AS status"),
        'created_at'
    );

    if ($this->search !== null) {
        $users->where(function ($query) {
            $query->where('username', 'like', "%$this->search%")
                ->orWhere('email', 'like', "%$this->search%")
                ->orWhere('no_bro', 'like', "%$this->search%")
                ->orWhere('mobile', 'like', "%$this->search%")
                ->orWhere('firstname', 'like', "%$this->search%")
                ->orWhere('lastname', 'like', "%$this->search%");
        });
    }

    if ($this->date !== null) {
        $date = explode('-', $this->date);

        if (!(strtotime($date[0]) && strtotime($date[1]))) {
            $notify[] = ['error', 'Please provide a valid date'];
            return back()->withNotify($notify);
        }

        $start = $date[0];
        $end = $date[1];

        $users->whereBetween('created_at', [Carbon::parse($start), Carbon::parse($end)->addDays(1)]);
    }

    return $users;
}

}
