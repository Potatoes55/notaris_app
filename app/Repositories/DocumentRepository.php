<?php

namespace App\Repositories;

use App\Models\Documents;
use App\Repositories\Interfaces\DocumentRepositoryInterface;

class DocumentRepository implements DocumentRepositoryInterface
{
    // public function all(string $status = '1')
    // {
    //     $query = Documents::query();

    //     if ($status === '1') {
    //         $query->where('status', 1);
    //     } elseif ($status === '0') {
    //         $query->where('status', 0);
    //     }

    //     return $query->paginate(10)->appends(request()->query());
    // }
    public function all(int $userId, ?string $status = null)
    {
        $query = Documents::query()->where('user_id', $userId);

        if ($status === '1') {
            $query->where('status', 1);
        } elseif ($status === '0') {
            $query->where('status', 0);
        }

        return $query->paginate(10)->appends(request()->query());
    }

    public function search(string $keyword, ?string $status = null)
    {
        $query = Documents::where(function ($q) use ($keyword) {
            $q->where('code', 'like', "%{$keyword}%")
                ->orWhere('name', 'like', "%{$keyword}%")
                ->orWhere('description', 'like', "%{$keyword}%");
        });

        if ($status === '1') {
            $query->where('status', 1);
        } elseif ($status === '0') {
            $query->where('status', 0);
        }

        return $query->paginate(10)->appends(request()->query());
    }

    public function create(array $data): Documents
    {
        return Documents::create($data);
    }

    public function update(Documents $document, array $data): Documents
    {
        $document->update($data);

        return $document;
    }

    public function deactivate(Documents $document): bool
    {
        $document->status = false;

        return $document->save();
    }

    public function activeDocument(Documents $document): bool
    {
        $document->status = true;

        return $document->save();
    }

    public function find(int $id): ?Documents
    {
        return Documents::find($id);
    }
}
