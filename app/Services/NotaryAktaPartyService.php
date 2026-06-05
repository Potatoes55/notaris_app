<?php

namespace App\Services;

use App\Models\NotaryAktaParties;
use App\Models\NotaryAktaTransaction;
use App\Repositories\Interfaces\NotaryAktaPartiesRepositoryInterface;

class NotaryAktaPartyService
{
    protected $repo;

    public function __construct(NotaryAktaPartiesRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

public function searchAkta(array $filters)
{
    return NotaryAktaTransaction::query()
        ->where('notaris_id', auth()->user()->notaris_id)
        ->where(function ($query) use ($filters) {
            
            if (!empty($filters['transaction_code'])) {
                $query->where('transaction_code', $filters['transaction_code']);
            }

            if (!empty($filters['akta_number'])) {
                $query->orWhere('akta_number', $filters['akta_number']);
            }
        })
        ->get();
}

    public function findParty(int $id)
    {
        return NotaryAktaParties::findOrFail($id);
    }

    public function getPartiesByAkta(int $aktaTransactionId, bool $paginate = false)
    {
        $query = NotaryAktaParties::where('akta_transaction_id', $aktaTransactionId)
            ->orderBy('created_at', 'desc');

        return $paginate
            ? $query->paginate(10)->withQueryString()
            : $query->get();
    }

    public function addParty(array $data)
    {
        return $this->repo->create($data);
    }

    public function store(array $data)
    {
        // Cari akta transaksi berdasarkan client_code
        $akta = NotaryAktaTransaction::where('client_code', $data['client_code'])->firstOrFail();

        $data['akta_transaction_id'] = $akta->id;
        $data['notaris_id'] = $akta->notaris_id;
        $data['client_id'] = $akta->client_id;

        return $this->repo->create($data);
    }

    public function updateParty(int $id, array $data)
    {
        return $this->repo->update($id, $data);
    }

    public function deleteParty(int $id)
    {
        return $this->repo->delete($id);
    }
}
