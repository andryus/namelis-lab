<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Clinic;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('admin')) {
            return Inertia::render('Dashboard/Admin', [
                'stats' => [
                    'total_orders'      => Order::count(),
                    'in_production'     => Order::where('status', 'in_production')->count(),
                    'awaiting_delivery' => Order::where('status', 'awaiting_delivery')->count(),
                    'delivered_month'   => Order::where('status', 'delivered')->whereMonth('updated_at', now()->month)->count(),
                    'total_clinics'     => Clinic::where('approved', true)->count(),
                ],
                'recent_orders' => Order::with('clinic')->latest()->take(8)->get(),
            ]);
        }

        if ($user->hasRole('lab')) {
            return Inertia::render('Dashboard/Lab', [
                'queue' => Order::with('clinic', 'items.workCategory')
                    ->whereIn('status', ['at_lab', 'in_production', 'quality_check'])
                    ->latest()->get(),
                'stats' => [
                    'at_lab'         => Order::where('status', 'at_lab')->count(),
                    'in_production'  => Order::where('status', 'in_production')->count(),
                    'quality_check'  => Order::where('status', 'quality_check')->count(),
                ],
            ]);
        }

        if ($user->hasRole('dentist')) {
            $clinic = Clinic::where('user_id', $user->id)->first();
            return Inertia::render('Dashboard/Dentist', [
                'clinic'  => $clinic,
                'orders'  => $clinic
                    ? Order::with('items.workCategory')->where('clinic_id', $clinic->id)->latest()->get()
                    : [],
                'stats' => [
                    'pending'    => $clinic ? Order::where('clinic_id', $clinic->id)->whereNotIn('status', ['delivered','cancelled'])->count() : 0,
                    'delivered'  => $clinic ? Order::where('clinic_id', $clinic->id)->where('status', 'delivered')->count() : 0,
                ],
            ]);
        }

        if ($user->hasRole('courier')) {
            return Inertia::render('Dashboard/Courier', [
                'pickups'   => Order::with('clinic')->where('status', 'awaiting_pickup')->latest()->get(),
                'deliveries'=> Order::with('clinic')->where('status', 'awaiting_delivery')->latest()->get(),
            ]);
        }

        return redirect('/');
    }
}
