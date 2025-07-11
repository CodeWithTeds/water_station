<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderApprovalRequest;
use App\Http\Requests\OrderCancellationRequest;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of all orders.
     */
    public function index(Request $request)
    {
        $status = $request->query('status');
        $activeTab = 'all';
        
        $orders = in_array($status, ['pending', 'processing', 'completed', 'cancelled'])
            ? $this->orderService->getOrdersByStatus($status)
            : $this->orderService->getAllOrders();
            
        $activeTab = $status ?? 'all';
        
        return view('admin.orders.index', compact('orders', 'activeTab'));
    }

    /**
     * Display the specified order.
     */
    public function show(int $id)
    {
        return view('admin.orders.show', [
            'order' => $this->orderService->getOrderById($id)
        ]);
    }

    /**
     * Approve the specified order.
     */
    public function approve(OrderApprovalRequest $request, int $id)
    {
        return $this->orderService->approveOrder($id, $request->validated())
            ? redirect()->route('admin.orders.show', $id)->with('success', 'Order has been approved and is now being processed.')
            : redirect()->route('admin.orders.show', $id)->with('error', 'There was an error approving the order. Please try again.');
    }

    /**
     * Mark the specified order as completed.
     */
    public function complete(int $id)
    {
        return $this->orderService->completeOrder($id)
            ? redirect()->route('admin.orders.show', $id)->with('success', 'Order has been marked as completed.')
            : redirect()->route('admin.orders.show', $id)->with('error', 'There was an error completing the order. Please try again.');
    }

    /**
     * Cancel the specified order.
     */
    public function cancel(OrderCancellationRequest $request, int $id)
    {
        return $this->orderService->cancelOrder($id, $request->validated()['reason'])
            ? redirect()->route('admin.orders.show', $id)->with('success', 'Order has been cancelled.')
            : redirect()->route('admin.orders.show', $id)->with('error', 'There was an error cancelling the order. Please try again.');
    }
} 