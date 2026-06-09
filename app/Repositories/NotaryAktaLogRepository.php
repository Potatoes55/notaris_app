<?php

namespace App\Repositories;

use App\Models\NotaryAktaLog;
use App\Models\NotaryAktaLogs;
use App\Repositories\Interfaces\NotaryAktaLogRepositoryInterface;

class NotaryAktaLogRepository implements NotaryAktaLogRepositoryInterface
{
    public function all(array $filters = [])
    {
        $query = NotaryAktaLogs::with(['notaris', 'client', 'akta_transaction'])->where('notaris_id', auth()->user()->notaris_id);

        if (!empty($filters['registration_code'])) {
            $query->where('registration_code', 'like', '%' . $filters['registration_code'] . '%');
        }

        if (!empty($filters['step'])) {
            $query->where('step', 'like', '%' . $filters['step'] . '%');
        }

        // Pagination (10 data per halaman)
        return $query->latest()->paginate(10)->withQueryString();
    }

    public function getById($id)
    {
        return NotaryAktaLogs::where('id', $id)
            ->where('notaris_id', auth()->user()->notaris_id)
            ->firstOrFail();
    }

    public function create(array $data)
    {
        return NotaryAktaLogs::create($data);
    }

    public function update($id, array $data)
    {
        $log = $this->getById($id);
        $log->update($data);
        return $log;
    }

    public function delete($id)
    {
        $log = $this->getById($id);
        return $log->delete();
    }
}
