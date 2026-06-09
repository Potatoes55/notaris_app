<?php

namespace App\Repositories;

use App\Models\PicHandOver;
use App\Repositories\Interfaces\PicHandoverRepositoryInterface;

class PicHandoverRepository implements PicHandoverRepositoryInterface
{
    public function all(array $filters = [])
    {
        $query = PicHandover::with(['picDocument'])->where('notaris_id', auth()->user()->notaris_id);

        // Ambil search dengan aman (tidak error meskipun tidak dikirim dari request)
        $search = $filters['search'] ?? null;

        if (!empty($search)) {
            $query->whereHas('picDocument', function ($q) use ($search) {
                $q->where('pic_document_code', 'like', '%' . $search . '%');
            });
        }

        return $query->latest()->paginate(10);
    }

    public function find($id)
    {
        return PicHandover::with(['picDocument'])
            ->where('id', $id)
            ->where('notaris_id', auth()->user()->notaris_id)
            ->firstOrFail();
    }

    public function create(array $data)
    {
        return PicHandover::create($data);
    }

    public function delete($id)
    {
        return PicHandover::where('id', $id)
            ->where('notaris_id', auth()->user()->notaris_id)
            ->delete();
    }
}
