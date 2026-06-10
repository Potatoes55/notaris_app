<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentRequest;
use App\Models\Documents;
use App\Services\DocumentService;
use Illuminate\Http\Request;

class DocumentsController extends Controller
{
    public function __construct(protected DocumentService $documentService) {}

    public function index(Request $request)
    {
        $search = $request->input('search');
        $userId = auth()->user()->notaris_id;
        $data['notaris_id'] = auth()->user()->notaris_id;

        $status = $request->has('status') ? $request->query('status') : null;
        $documents = $this->documentService->getAll($userId, $status);
        $documents->appends($request->query());

        return view('pages.Documents.index', compact('documents'));
    }

    public function create()
    {
        return view('pages.Documents.form');
    }

    public function store(DocumentRequest $request)
    {
        $validated = $request->validated();

        try {
            // if ($request->hasFile('image')) {
            //     $validated['image'] = $request->file('image')->storeAs('img', $request->file('image')->getClientOriginalName());
            // }
            $validated['notaris_id'] = auth()->user()->notaris_id;
            $result = $this->documentService->createDocument($validated);

            notyf()->position('x', 'right')->position('y', 'top')->success('Berhasil menambahkan data jenis warkah');

            return redirect()->route('documents.index');
        } catch (\Exception $e) {
            notyf()->position('x', 'right')->position('y', 'top')->error('Gagal menambahkan data jenis warkah');

            return redirect()->back()->withInput();
            dd($e->getMessage());
        }
    }

    public function edit($id)
    {
        $document = $this->documentService->findProduct($id);

        return view('pages.Documents.form', compact('document'));
    }

    public function update(DocumentRequest $request, $id)
    {
        $validated = $request->validated();
        $validated['notaris_id'] = auth()->user()->notaris_id;

        // dd($document, $validated);

        $document = Documents::findOrFail($id);
        $document->update($validated);

        notyf()->position('x', 'right')->position('y', 'top')->success('Berhasil mengubah data jenis warkah');

        return redirect()->route('documents.index');
    }

    public function deactivate($id)
    {
        try {
            $this->documentService->deactivate($id);
            notyf()->position('x', 'right')->position('y', 'top')->success('Jenis Warkah berhasil dinonaktifkan.');

            return redirect()->route('documents.index');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function activate($id)
    {
        try {
            $this->documentService->activeDocument($id);

            notyf()
                ->position('x', 'right')
                ->position('y', 'top')
                ->success('Jenis Warkah berhasil diaktifkan.');

            return redirect()->route('documents.index');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
