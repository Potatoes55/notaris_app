<?php

namespace App\Services;

use App\Models\NotaryRelaasAkta;
use App\Models\NotaryRelaasDocument;

class NotaryRelaasDocumentService
{
    /**
     * Cari relaas berdasarkan registration_code atau relaas_number
     */
    public function searchRelaas(?string $transactionCode, ?string $relaasNumber, ?string $created_at)
    {
        return NotaryRelaasAkta::where('notaris_id', auth()->user()->notaris_id)
            ->where(function ($q) use ($transactionCode, $relaasNumber, $created_at) {

                if ($transactionCode) {
                    $q->where('transaction_code', $transactionCode);
                }

                if ($relaasNumber) {
                    $q->orWhere('relaas_number', $relaasNumber);
                }
                if ($created_at) {
                    $q->orWhere('created_at', $created_at);

                }
            })
            ->first();
    }

    public function searchRelaasByDateRange(string $startDate, string $endDate)
    {
        return NotaryRelaasAkta::with(['client'])
            ->withCount('documents') // Memastikan relasi 'documents' sudah ada di model NotaryRelaasAkta
            ->where('notaris_id', auth()->user()->notaris_id)
            ->whereHas('documents', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('story_date', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
            })
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
