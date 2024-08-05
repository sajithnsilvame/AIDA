<?php

namespace App\Filters\Tenant;

use App\Filters\Core\BaseFilter;
use Illuminate\Database\Eloquent\Builder;

class SalesReturnFilter extends BaseFilter
{
    public function search($search = null)
    {
        $this->builder->when($search, function (Builder $builder) use ($search) {
            $builder->where('order_invoice_number', 'LIKE', "%{$search}%")
                ->orWhere('invoice_number', 'LIKE', "%{$search}%");
        });
    }

    public function date($date = null)
    {
        $date = json_decode(htmlspecialchars_decode($date), true);

        $this->builder->when($date, function (Builder $builder) use ($date) {
            $builder->whereBetween(\DB::raw('DATE(created_at)'), array_values($date));
        });
    }

    public function rangeFilter($range = null)
    {
        $chargeRange = json_decode(htmlspecialchars_decode($range), true);

        $this->builder->when($range, function (Builder $builder) use ($chargeRange) {
            $builder->where('sub_total', '>=', $chargeRange['min'])
                ->where('sub_total', '<=', $chargeRange['max']);
        });
    }


    public function customers($value = null)
    {
        $this->builder->when($value, function (Builder $builder) use ($value) {
            $builder->whereIn('customer_id', explode(',', $value));
        });
    }
}