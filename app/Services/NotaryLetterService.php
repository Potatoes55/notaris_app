<?php

namespace App\Services;

use App\Repositories\Interfaces\NotaryLetterRepositoryInterface;

class NotaryLetterService
{
    protected $repository;

    public function __construct(NotaryLetterRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAll(?string $search = null, string $letterType = 'surat_keluar')
    {
        return $this->repository->all($search, $letterType);
    }

    public function getById($id)
    {
        return $this->repository->find($id);
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}
