<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'orders_total' => Order::count(),
                'revenue' => (int) Order::where('payment_status', PaymentStatus::Paye)->sum('total'),
                'orders_pending' => Order::where('status', OrderStatus::EnAttente)->count(),
                'products_active' => Product::active()->count(),
                'products_out_of_stock' => Product::active()->where('stock', 0)->count(),
            ],
            'recentOrders' => Order::with('customer')->latest()->limit(8)->get(),
        ]);
    }
}
