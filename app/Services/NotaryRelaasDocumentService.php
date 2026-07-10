<?php

namespace App\Services;

use App\Models\NotaryRelaasAkta;
use App\Models\NotaryRelaasDocument;

class NotaryRelaasDocumentService
{
    public function searchRelaas(array $filters = [])
    {
        $query = NotaryRelaasAkta::where('notaris_id', auth()->user()->notaris_id);

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', $search)
                    ->orWhere('relaas_number', $search);
            });
        }

        return $query->with(['client', 'akta_type', 'documents'])->first();
    }

    public function searchRelaasByDateRange(string $startDate, string $endDate)
    {
        return NotaryRelaasAkta::with(['client'])
            ->withCount('documents')
            ->where('notaris_id', auth()->user()->notaris_id)
            ->whereBetween('story_date', [$startDate.' 00:00:00', $endDate.' 23:59:59'])
            // ->whereHas('documents')
            ->orderBy('story_date', 'desc')
            ->paginate(10)
            ->withQueryString();
    }

    /**
     * Ambil semua dokumen dari relaas tertentu
     */
    public function getDocuments(int $relaasId)
    {
        return NotaryRelaasDocument::where('relaas_id', $relaasId)
            ->where('notaris_id', auth()->user()->notaris_id)
            ->orderBy('uploaded_at', 'desc')
            ->paginate(10);
    }

    /**
     * Cari dokumen berdasarkan ID
     */
    public function findById(int $id)
    {
        return NotaryRelaasDocument::findOrFail($id);
    }

    /**
     * Simpan dokumen baru
     */
    public function store(array $data)
    {
        return NotaryRelaasDocument::create($data);
    }

    /**
     * Update dokumen
     */
    public function update(int $id, array $data)
    {
        $document = $this->findById($id);
        $document->update($data);

        return $document;
    }

    /**
     * Hapus dokumen
     */
    public function destroy(int $id)
    {
        $document = $this->findById($id);

        return $document->delete();
    }

    /**
     * Toggle status dokumen (misal: active / inactive)
     */
    public function toggleStatus(int $id)
    {
        $document = $this->findById($id);
        $document->status = ! $document->status;
        $document->save();

        return $document;
    }
}
