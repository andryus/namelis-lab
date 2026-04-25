<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Clinic;
use App\Models\WorkCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Picqer\Barcode\BarcodeGeneratorSVG;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status']);

        $orders = Order::with('clinic', 'items')
            ->when(auth()->user()->hasRole('dentist'), function ($q) {
                $clinic = Clinic::where('user_id', auth()->id())->first();
                return $q->where('clinic_id', $clinic?->id);
            })
            ->when($filters['search'] ?? null, fn($q, $s) =>
                $q->where('reference', 'like', "%$s%")->orWhere('patient_ref', 'like', "%$s%")
            )
            ->when($filters['status'] ?? null, fn($q, $s) => $q->where('status', $s))
            ->latest()->paginate(20)->withQueryString();

        return Inertia::render('Orders/Index', [
            'orders'  => $orders,
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        if (!auth()->user()->hasRole('dentist')) {
            return redirect()->route('dashboard')
                ->with('error', 'Apenas médicos dentistas podem criar pedidos.');
        }
        $categories = WorkCategory::where('active', true)->orderBy('level')->orderBy('sort_order')->get();
        return Inertia::render('Orders/Create', compact('categories'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasRole('dentist')) {
            return redirect()->route('dashboard')
                ->with('error', 'Apenas médicos dentistas podem criar pedidos.');
        }

        $data = $request->validate([
            'patient_ref'             => 'required|string|max:100',
            'impression_type'         => 'required|in:physical,digital',
            'stl_external_url'        => 'nullable|url',
            'requested_delivery_date' => 'nullable|date|after:today',
            'clinical_notes'          => 'nullable|string|max:2000',
            'teeth'                   => 'required|array|min:1',
            'items'                   => 'required|array|min:1',
            'items.*.tooth_fdi'       => 'required|integer|between:11,48',
            'items.*.category_l3'     => 'required|exists:work_categories,id',
            'items.*.vita_shade'      => 'nullable|string|max:10',
            'items.*.finishing_stage' => 'nullable|in:direct,biscuit,metallic',
        ]);

        $clinic = Clinic::where('user_id', auth()->id())->firstOrFail();

        $order = Order::create([
            'reference'               => Order::nextReference(),
            'clinic_id'               => $clinic->id,
            'patient_ref'             => $data['patient_ref'],
            'impression_type'         => $data['impression_type'],
            'stl_external_url'        => $data['stl_external_url'] ?? null,
            'requested_delivery_date' => $data['requested_delivery_date'] ?? null,
            'clinical_notes'          => $data['clinical_notes'] ?? null,
            'status'                  => $data['impression_type'] === 'digital' ? 'at_lab' : 'awaiting_pickup',
            'barcode'                 => 'NML' . str_pad(Order::count() + 1, 8, '0', STR_PAD_LEFT),
        ]);

        foreach ($data['items'] as $item) {
            $cat = WorkCategory::find($item['category_l3']);
            $order->items()->create([
                'tooth_fdi'        => $item['tooth_fdi'],
                'work_category_id' => $item['category_l3'],
                'vita_shade'       => $item['vita_shade'] ?? null,
                'finishing_stage'  => $item['finishing_stage'] ?? 'direct',
                'unit_price'       => $cat?->base_price,
            ]);
        }

        return redirect()->route('orders.show', $order)->with('success', 'Pedido criado com sucesso!');
    }

    public function show(Order $order)
    {
        $order->load([
            'clinic',
            'items.workCategory.parent.parent',
            'assignedTech',
        ]);
        return Inertia::render('Orders/Show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate(['status' => 'required|string']);
        $order->update(['status' => $data['status']]);
        return back()->with('success', 'Estado atualizado.');
    }

    public function pdf(Order $order)
    {
        $order->load([
            'clinic',
            'items.workCategory.parent.parent',
            'assignedTech',
        ]);

        // Generate barcode SVG inline
        $barcodeHtml = '';
        if ($order->barcode) {
            $generator   = new BarcodeGeneratorSVG();
            $barcodeHtml = $generator->getBarcode(
                $order->barcode,
                BarcodeGeneratorSVG::TYPE_CODE_128,
                1.8,
                50
            );
        }

        $statusLabels = [
            'draft'             => 'Rascunho',
            'awaiting_pickup'   => 'Aguardar Recolha',
            'in_transit'        => 'Em Trânsito',
            'at_lab'            => 'No Laboratório',
            'in_production'     => 'Em Produção',
            'quality_check'     => 'Controlo de Qualidade',
            'awaiting_delivery' => 'Aguardar Entrega',
            'delivered'         => 'Entregue',
            'cancelled'         => 'Cancelado',
        ];

        $impressionLabels = [
            'physical' => 'Moldagem Física',
            'digital'  => 'Scan Digital (STL)',
            'mixed'    => 'Misto',
        ];

        $pdf = Pdf::loadView('pdf.order', compact(
            'order',
            'barcodeHtml',
            'statusLabels',
            'impressionLabels'
        ))
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'defaultFont'         => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'     => false,
        ]);

        return $pdf->download('guia-trabalho-' . $order->reference . '.pdf');
    }
    public function edit(Order $order) {}
    public function update(Request $request, Order $order) {}
    public function destroy(Order $order) {}
}
