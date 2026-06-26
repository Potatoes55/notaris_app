<?php

namespace App\Http\Controllers;

use App\Models\PicDocuments;
use App\Services\PicProcessService;
use Illuminate\Http\Request;

class PicProcessController extends Controller
{
    protected $service;

    public function __construct(PicProcessService $service)
    {
        $this->service = $service;
    }

    private function getModule()
    {
        return request()->routeIs('ppat.*') ? 'PPAT' : 'Notaris';
    }

    private function getIndexRoute()
    {
        return request()->routeIs('ppat.*')
            ? 'ppat.pic.process'
            : 'notaris.pic.process';
    }

    public function index(Request $request)
    {
        $module = $this->getModule();

        $processes = collect();
        $doc = null;

        if ($request->filled('pic_document_code')) {

            $doc = PicDocuments::with(['pic', 'client'])
                ->where('pic_document_code', $request->pic_document_code)
                ->where('notaris_id', auth()->user()->notaris_id)
                ->first();

            if ($doc) {
                $processes = $this->service->listProcesses([
                    'pic_document_id' => $doc->id
                ]);
            } else {
                notyf()
                    ->position('x', 'right')
                    ->position('y', 'top')
                    ->warning('Kode dokumen tidak ditemukan.');
            }
        }

        return view(
            'pages.PIC.PicProcess.index',
            compact('processes', 'doc', 'module')
        );
    }

    public function create(Request $request)
    {
        $module = $this->getModule();

        $picDocument = null;

        if ($request->filled('pic_document_code')) {
            $picDocument = PicDocuments::where(
                'pic_document_code',
                $request->pic_document_code
            )->first();
        }

        return view(
            'pages.PIC.PicProcess.form',
            compact('picDocument', 'module')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pic_document_id' => 'required',
            'step_name'       => 'required',
            'step_status'     => 'required',
            'step_date'       => 'required',
            'note'            => 'nullable',
        ]);

        $validated['notaris_id'] = auth()->user()->notaris_id;

        $this->service->createProcess($validated);

        notyf()
            ->position('x', 'right')
            ->position('y', 'top')
            ->success('Proses pengurusan berhasil ditambahkan.');

        return redirect()->route(
            $this->getIndexRoute(),
            [
                'pic_document_code' => $request->pic_document_code
            ]
        );
    }

    public function edit($id)
    {
        $module = $this->getModule();

        $process = $this->service->getProcessById($id);

        $process->load('pic_document');

        return view(
            'pages.PIC.PicProcess.form',
            compact('process', 'module')
        );
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'pic_document_id' => 'nullable',
            'step_name'       => 'required',
            'step_status'     => 'required',
            'step_date'       => 'required',
            'note'            => 'nullable',
        ]);

        $validated['notaris_id'] = auth()->user()->notaris_id;

        $this->service->updateProcess($id, $validated);

        notyf()
            ->position('x', 'right')
            ->position('y', 'top')
            ->success('Proses pengurusan berhasil diperbarui.');

        return redirect()->route(
            $this->getIndexRoute(),
            [
                'pic_document_code' => $request->pic_document_code
            ]
        );
    }

    public function destroy($id)
    {
        $this->service->deleteProcess($id);

        notyf()
            ->position('x', 'right')
            ->position('y', 'top')
            ->success('Proses pengurusan berhasil dihapus.');

        return back();
    }

    public function indexProcess(Request $request)
    {
        $processes = collect();

        if ($request->filled('pic_document_code')) {

            $doc = PicDocuments::where(
                'pic_document_code',
                $request->pic_document_code
            )->first();

            if ($doc) {
                $processes = $this->service->listProcesses([
                    'pic_document_id' => $doc->id
                ]);
            } else {
                notyf()
                    ->position('x', 'right')
                    ->position('y', 'top')
                    ->error('Kode dokumen tidak ditemukan.');
            }
        }

        return view(
            'pages.ManagementProcess.index2',
            compact('processes')
        );
    }
}