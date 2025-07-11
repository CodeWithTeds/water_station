<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminCreateSaleRequest;
use App\Services\AdminSalesService;
use Illuminate\Support\Facades\Log;


class SalesController extends Controller
{
    protected AdminSalesService $salesService;

    public function __construct(AdminSalesService $salesService)
    {
        $this->salesService = $salesService;
    }

    public function create()
    {
        return view('admin.sales.create', [
            'products' => $this->salesService->getProducts()
        ]);
    }

    public function store(AdminCreateSaleRequest $request)
    {
        try {
            $validatedData = $request->validated();
            
            // Process items from the form
            $items = [];
            $formItems = $request->input('items', []);
            
            foreach ($formItems as $productId => $itemData) {
                $quantity = (int) ($itemData['quantity'] ?? 0);
                if ($quantity > 0) {
                    $items[] = [
                        'product_id' => (int) $itemData['product_id'],
                        'quantity' => $quantity
                    ];
                }
            }
            
            // Validate that at least one item is selected
            if (empty($items)) {
                return redirect()->back()
                    ->with('error', 'Please select at least one product.')
                    ->withInput();
            }
            
            $validatedData['items'] = $items;
            
            $this->salesService->createSale($validatedData);
            return redirect()->route('admin.sales.index')->with('success', 'Sale recorded successfully!');
        } catch (\Exception $e) {
            Log::error('Error creating sale: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error creating sale: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function index()
    {
        return view('admin.sales.index', [
            'sales' => $this->salesService->getAllSales()
        ]);
    }
    public function show($id)
    {
        return view('admin.sales.show', [
            'sale' => $this->salesService->getSale($id)
        ]);
    }
    public function edit($id)
    {
        $sale = $this->salesService->getSale($id);
        $products = $this->salesService->getProducts();
        
        // Prepare items data for JavaScript
        $itemsData = $sale->items->map(function($item) {
            return [
                'productId' => (int) $item->product_id,
                'name' => $item->product->name ?? '',
                'price' => (float) $item->unit_price,
                'quantity' => (int) $item->quantity,
                'subtotal' => (float) $item->subtotal,
            ];
        });
        
        return view('admin.sales.edit', [
            'sale' => $sale,
            'products' => $products,
            'itemsData' => $itemsData
        ]);
    }
    public function update(\App\Http\Requests\AdminEditSaleRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();
            
            // Filter out items with quantity 0
            $items = [];
            foreach ($validatedData['items'] as $itemData) {
                if ((int) $itemData['quantity'] > 0) {
                    $items[] = [
                        'product_id' => (int) $itemData['product_id'],
                        'quantity' => (int) $itemData['quantity']
                    ];
                }
            }
            
            $validatedData['items'] = $items;
            
            $this->salesService->updateSale($id, $validatedData);
            return redirect()->route('admin.sales.index')->with('success', 'Sale updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating sale: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error updating sale: ' . $e->getMessage())
                ->withInput();
        }
    }
    public function destroy($id)
    {
        $this->salesService->deleteSale($id);
        return redirect()->route('admin.sales.index')->with('success', 'Sale deleted!');
    }
} 