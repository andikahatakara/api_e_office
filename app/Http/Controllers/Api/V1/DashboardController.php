<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\IncomingLetter;
use App\Models\OutgoingLetter;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Retrive all user notifications
     * @return \App\Helpers\ApiResponseFormatter
     * @krismonsemanas
    */
    public function notifications() : JsonResponse
    {
        $user = User::find(Auth::id());
        return ApiResponseFormatter::success($user->unreadNotifications, 'Berhasil mendapatkan data');
    }

    /**
     * Get overview dashboard
     * @param \Illuminate\Http\Request $request
     * @return \App\Helpers\ApiResponseFormatter
     * @krismonsemanas
    */
    public function overview(Request $request) : JsonResponse
    {
        $now = now();
        $year = $request->query('year') ?? $now->year;

        $countOfOutgoing = OutgoingLetter::whereYear('date', $now->year)->count();
        $countOfIncoming = IncomingLetter::whereYear('date', $now->year)->count();
        $outgoings = DB::table('outgoing_letters')
                    ->whereYear('date', $year)
                    ->selectRaw('COUNT(id) overview, MONTH(date) as month ')
                    ->groupByRaw('MONTH(date)')
                    ->get();

        $incomings = DB::table('incoming_letters')
                    ->whereYear('date', $year)
                    ->selectRaw('COUNT(id) overview, MONTH(date) as month ')
                    ->groupByRaw('MONTH(date)')
                    ->get();

        $outgoingLabels = $outgoings->map(function($outgoing) {
            return $this->month($outgoing->month);
        });
        $incomingLabels = $incomings->map(function($incoming) {
            return $this->month($incoming->month);
        });



        $overviews = [
            'countOfOutgoing' => $countOfOutgoing,
            'countOfIncoming' => $countOfIncoming,
            'outgoing' => [
                'labels' => $outgoingLabels,
                'datasets' => [
                    [
                        'label' => 'Surat Keluar',
                        'data' =>  $outgoings->pluck('overview'),
                        'color' => 'danger'
                    ]
                ]
            ],
            'incoming' => [
                'labels' => $incomingLabels,
                'datasets' => [
                    [
                        'label' => 'Surat Masuk',
                        'data' => $incomings->pluck('overview'),
                        'color' => 'success'
                    ]
                ]
            ],
        ];
        return ApiResponseFormatter::success($overviews, 'Berhasil medapatkan overview data');
    }

    private function month(string $month) : string
    {
        $data = [
            '1' => 'Januari',
            '2' => 'Februari',
            '3' => 'Maret',
            '4' => 'April',
            '5' => 'Mei',
            '6' => 'Juni',
            '7' => 'Juli',
            '8' => 'Agustus',
            '9' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];

        return $data[$month];
    }
}
