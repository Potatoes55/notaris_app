<?php

namespace App\Services;

use App\Repositories\Interfaces\NotaryCostRepositoryInterface;

class NotaryCostService
{
    protected $repository;

    public function __construct(NotaryCostRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function list(array $filters = [])
    {
        return $this->repository->all($filters);
    }

    public function detail($id)
    {
        return $this->repository->find($id);
    }

    public function create(array $data)
    {
        $data['notaris_id'] = auth()->user()->notaris_id;

        // Bersihkan input currency jadi angka mentah
        $data['product_cost'] = (int) str_replace('.', '', $data['product_cost'] ?? 0);
        $data['admin_cost'] = (int) str_replace('.', '', $data['admin_cost'] ?? 0);
        $data['other_cost'] = (int) str_replace('.', '', $data['other_cost'] ?? 0);
        $data['amount_paid'] = (int) str_replace('.', '', $data['amount_paid'] ?? 0);
        $data['pph'] = (int) str_replace('.', '', $data['pph'] ?? 0);
        $data['bphtb'] = (int) str_replace('.', '', $data['bphtb'] ?? 0);

        // Hitung total
        $data['total_cost'] = $data['product_cost'] + $data['admin_cost'] + $data['other_cost'] + $data['pph'] + $data['bphtb'];

        return $this->repository->create($data);
    }

    public function update($id, array $data)
    {
        $data['notaris_id'] = auth()->user()->notaris_id;

        // Bersihkan input currency jadi angka mentah
        $data['product_cost'] = (int) str_replace('.', '', $data['product_cost'] ?? 0);
        $data['admin_cost'] = (int) str_replace('.', '', $data['admin_cost'] ?? 0);
        $data['other_cost'] = (int) str_replace('.', '', $data['other_cost'] ?? 0);
        $data['amount_paid'] = (int) str_replace('.', '', $data['amount_paid'] ?? 0);
        $data['pph'] = (int) str_replace('.', '', $data['pph'] ?? 0);
        $data['bphtb'] = (int) str_replace('.', '', $data['bphtb'] ?? 0);
        // Hitung total
        $data['total_cost'] = $data['product_cost'] + $data['admin_cost'] + $data['other_cost'] - $data['amount_paid'] - $data['pph'] - $data['bphtb'];

        return $this->repository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function findByPaymentCode($code)
    {
        return $this->repository->findByPaymentCode($code);
    }
}
