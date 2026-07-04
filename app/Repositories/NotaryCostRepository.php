<?php

namespace App\Repositories;

use App\Models\NotaryCost;
use App\Repositories\Interfaces\NotaryCostRepositoryInterface;

class NotaryCostRepository implements NotaryCostRepositoryInterface
{
    public function all(array $filters = [])
    {
        return NotaryCost::with(['client', 'picDocument', 'payments'])
            ->where('notaris_id', auth()->user()->notaris_id)
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->whereHas('picDocument', function ($query) use ($search) {
                    $query->where('pic_document_code', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);
    }

    public function find($id)
    {
        return NotaryCost::with(['client', 'picDocument'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return NotaryCost::create($data);
    }

    public function update($id, array $data)
    {
        $cost = NotaryCost::findOrFail($id);
        $cost->update($data);

        return $cost;
    }

    public function delete($id)
    {
        return NotaryCost::destroy($id);
    }

    public function findByPaymentCode($code)
    {
        return NotaryCost::where('payment_code', $code)->with(['client', 'picDocument'])->first();
    }
}
