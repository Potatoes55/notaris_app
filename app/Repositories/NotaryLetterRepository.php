<?php

namespace App\Repositories;

use App\Models\NotaryLetters;
use App\Repositories\Interfaces\NotaryLetterRepositoryInterface;

class NotaryLetterRepository implements NotaryLetterRepositoryInterface
{
    public function all(?string $search = null, string $letterType = 'surat_keluar')
    {
        $query = NotaryLetters::with(['notaris', 'client'])
            ->where('notaris_id', auth()->user()->notaris_id)
            ->where('letter_type', $letterType)
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('letter_number', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%")
                        ->orWhere('recipient', 'like', "%{$search}%");
                });
            })
            ->latest();

        // 🔍 Tambahkan 2 baris ini untuk melihat query SQL & parameter $letterType
        // dd($letterType, $query->toRawSql());

        return $query->paginate(10);
    }

    public function find($id): ?NotaryLetters
    {
        return NotaryLetters::with(['notaris', 'client'])->find($id);
    }

    public function create(array $data): NotaryLetters
    {
        return NotaryLetters::create($data);
    }

    public function update($id, array $data): bool
    {
        $notaryLetter = $this->find($id);

        return $notaryLetter->update($data);
    }

    public function delete($id): bool
    {
        $notaryLetter = $this->find($id);

        return $notaryLetter->delete();
    }
}
