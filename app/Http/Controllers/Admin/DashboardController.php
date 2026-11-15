<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $summary = DB::selectOne(<<<'SQL'
            SELECT
                (SELECT COUNT(*) FROM users) AS total_users,
                (SELECT COUNT(*) FROM users WHERE status = 'pending') AS pending_users,
                (SELECT COUNT(*) FROM users WHERE status = 'approved') AS approved_users,
                (SELECT COALESCE(SUM(balance), 0) FROM accounts) AS wallet_balance,
                (SELECT COALESCE(SUM(amount), 0) FROM recharges WHERE status = 'approved') AS bd_recharge_total,
                (SELECT COALESCE(SUM(amount), 0) FROM male_recharges WHERE status = 'approved') AS male_recharge_total,
                (SELECT COALESCE(SUM(amount), 0) FROM package_orderls WHERE status = 'approved') AS package_total,
                (SELECT COALESCE(SUM(amount), 0) FROM mobile_banking_orders WHERE status = 'approved') AS mobile_banking_total,
                (SELECT COALESCE(SUM(amount), 0) FROM ibanking_orders WHERE status = 'approved') AS ibanking_total,
                (SELECT COUNT(*) FROM transactions WHERE status = 'pending') AS pending_balance,
                (SELECT COUNT(*) FROM recharges WHERE status = 'pending') AS pending_bd_recharge,
                (SELECT COUNT(*) FROM male_recharges WHERE status = 'pending') AS pending_male_recharge,
                (SELECT COUNT(*) FROM package_orderls WHERE status = 'pending') AS pending_packages,
                (SELECT COUNT(*) FROM mobile_banking_orders WHERE status = 'pending') AS pending_mobile_banking,
                (SELECT COUNT(*) FROM ibanking_orders WHERE status = 'pending') AS pending_ibanking,
                (SELECT COUNT(*) FROM calling_cards WHERE buy_now_status = 0) AS available_calling_cards,
                (SELECT COUNT(*) FROM vpns WHERE status = 'active' AND buy_now_status = 0 AND expire_date >= CURRENT_DATE) AS available_vpns,
                (SELECT COUNT(*) FROM transactions WHERE DATE(created_at) = CURRENT_DATE) +
                (SELECT COUNT(*) FROM recharges WHERE DATE(created_at) = CURRENT_DATE) +
                (SELECT COUNT(*) FROM male_recharges WHERE DATE(created_at) = CURRENT_DATE) +
                (SELECT COUNT(*) FROM package_orderls WHERE DATE(created_at) = CURRENT_DATE) +
                (SELECT COUNT(*) FROM mobile_banking_orders WHERE DATE(created_at) = CURRENT_DATE) +
                (SELECT COUNT(*) FROM ibanking_orders WHERE DATE(created_at) = CURRENT_DATE) AS today_activity
        SQL);

        $packages = DB::table('packages')
            ->leftJoin('package_details', 'packages.id', '=', 'package_details.package_id')
            ->leftJoin('package_orderls', function ($join) {
                $join->on('package_details.id', '=', 'package_orderls.package_id')
                    ->where('package_orderls.status', 'approved');
            })
            ->where('packages.status', 'active')
            ->select('packages.id', 'packages.name', 'packages.image_icon')
            ->selectRaw('COALESCE(SUM(package_orderls.amount), 0) AS total_amount')
            ->selectRaw('COUNT(package_orderls.id) AS order_count')
            ->groupBy('packages.id', 'packages.name', 'packages.image_icon')
            ->orderByDesc('total_amount')
            ->get();

        $mobileBanking = DB::table('mobile_bankings')
            ->leftJoin('mobile_banking_orders', function ($join) {
                $join->on('mobile_bankings.id', '=', 'mobile_banking_orders.mobile_banking_id')
                    ->where('mobile_banking_orders.status', 'approved');
            })
            ->select('mobile_bankings.id', 'mobile_bankings.name', 'mobile_bankings.image_icon', 'mobile_bankings.status')
            ->selectRaw('COALESCE(SUM(mobile_banking_orders.amount), 0) AS total_amount')
            ->selectRaw('COUNT(mobile_banking_orders.id) AS order_count')
            ->groupBy('mobile_bankings.id', 'mobile_bankings.name', 'mobile_bankings.image_icon', 'mobile_bankings.status')
            ->orderByDesc('total_amount')
            ->get();

        $banks = DB::table('bank_names')
            ->leftJoin('ibanking_orders', function ($join) {
                $join->on('bank_names.id', '=', 'ibanking_orders.bank_name_id')
                    ->where('ibanking_orders.status', 'approved');
            })
            ->select('bank_names.id', 'bank_names.bank_name', 'bank_names.status')
            ->selectRaw('COALESCE(SUM(ibanking_orders.amount), 0) AS total_amount')
            ->selectRaw('COUNT(ibanking_orders.id) AS order_count')
            ->groupBy('bank_names.id', 'bank_names.bank_name', 'bank_names.status')
            ->orderByDesc('total_amount')
            ->get();

        $sidebarCounts = [
            'users' => (int) $summary->pending_users,
            'calling_cards' => (int) $summary->available_calling_cards,
            'vpns' => (int) $summary->available_vpns,
            'balance' => (int) $summary->pending_balance,
            'packages' => (int) $summary->pending_packages,
            'bd_recharge' => (int) $summary->pending_bd_recharge,
            'male_recharge' => (int) $summary->pending_male_recharge,
            'mobile_banking' => (int) $summary->pending_mobile_banking,
            'ibanking' => (int) $summary->pending_ibanking,
        ];

        $pendingTotal = collect($sidebarCounts)
            ->except(['calling_cards', 'vpns'])
            ->sum();

        $nextPending = collect([
            ['count' => (int) $summary->pending_users, 'label' => 'Review user approvals', 'url' => route('admin.users.list')],
            ['count' => (int) $summary->pending_balance, 'label' => 'Review balance requests', 'url' => route('admin.balance.pending')],
            ['count' => (int) $summary->pending_bd_recharge, 'label' => 'Review BD recharges', 'url' => route('admin.recharges.pending')],
            ['count' => (int) $summary->pending_male_recharge, 'label' => 'Review Maldives recharges', 'url' => route('admin.male.recharges.pending')],
            ['count' => (int) $summary->pending_packages, 'label' => 'Review package orders', 'url' => route('admin.package.orders')],
            ['count' => (int) $summary->pending_mobile_banking, 'label' => 'Review mobile banking orders', 'url' => route('admin.mobile.banking.orders.list')],
            ['count' => (int) $summary->pending_ibanking, 'label' => 'Review iBanking orders', 'url' => route('admin.ibanking.orders.list')],
        ])->first(fn (array $item) => $item['count'] > 0);

        return view('admin.dashboard', compact(
            'summary',
            'packages',
            'mobileBanking',
            'banks',
            'sidebarCounts',
            'pendingTotal',
            'nextPending'
        ));
    }
}
