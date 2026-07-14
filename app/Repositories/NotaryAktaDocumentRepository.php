<?php

namespace App\Repositories;

use App\Models\NotaryAktaDocuments;
use App\Repositories\Interfaces\NotaryAktaDocumentRepositoryInterface;

class NotaryAktaDocumentRepository implements NotaryAktaDocumentRepositoryInterface
{
    public function all(array $filters = [])
    {
        $query = NotaryAktaDocuments::query()->where('notaris_id', auth()->user()->notaris_id);

        if (! empty($filters['akta_transaction_id'])) {
            $query->where('akta_transaction_id', $filters['akta_transaction_id']);
        }

        if (! empty($filters['client_code'])) {
            $query->where('client_code', 'like', '%'.$filters['client_code'].'%');
        }

        if (! empty($filters['akta_number'])) {
            $query->whereHas('akta_transaction', function ($q) use ($filters) {
                $q->where('akta_number', 'like', '%'.$filters['akta_number'].'%');
            });
        }

        if (! empty($filters['fullname'])) {
            $query->whereHas('client', function ($q) use ($filters) {
                $q->where('fullname', 'like', '%'.$filters['fullname'].'%');
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
    }

    public function getById($id)
    {
        return NotaryAktaDocuments::findOrFail($id);
    }

    public function create(array $data)
    {
        return NotaryAktaDocuments::create($data);
    }

    public function update($id, array $data)
    {
        $document = $this->getById($id);
        $document->update($data);

        return $document;
    }

    public function delete($id)
    {
        $document = $this->getById($id);

        return $document->delete();
    }
}
