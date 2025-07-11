<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'customer_name',
        'status',
        'total_amount',
        'delivery_address',
        'notes',
        'order_type',
        'payment_method',
        'payment_status',
        'delivery_date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the customer name, handling both registered customers and walk-in sales
     *
     * @return string
     */
    public function getCustomerNameAttribute()
    {
        if ($this->customer) {
            return $this->customer->fullname;
        }
        
        return $this->customer_name ?? 'Walk-in Customer';
    }

    /**
     * Check if this is a walk-in sale
     *
     * @return bool
     */
    public function isWalkInSale()
    {
        return is_null($this->customer_id);
    }
} 