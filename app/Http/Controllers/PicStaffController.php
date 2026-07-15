<?php

namespace App\Http\Controllers;

use App\Models\PicStaff;
use App\Services\PicStaffService;
use Illuminate\Http\Request;

class PicStaffController extends Controller
{
    protected $service;

    public function __construct(PicStaffService $service)
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
            ? 'ppat.pic.staff'
            : 'notaris.pic.staff';
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $module = $this->getModule();

        $picStaffs = $this->service->getAll($search);

        return view(
            'pages.PIC.PicStaff.index',
            compact('picStaffs', 'search', 'module')
        );
    }

    public function create()
    {
        $module = $this->getModule();

        return view(
            'pages.PIC.PicStaff.form',
            compact('module')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'full_name' => 'required|string|max:255',
                'email' => 'nullable|email',
                'phone_number' => 'required|string|max:50',
                'position' => 'required|string|max:100',
                'address' => 'required|string|max:255',
                'note' => 'nullable|string|max:255',
            ],
            [
                'full_name.required' => 'Nama lengkap harus diisi.',
                'email.email' => 'Format email tidak valid.',
                'phone_number.required' => 'Nomor telepon harus diisi.',
                'position.required' => 'Posisi harus diisi.',
                'address.required' => 'Alamat harus diisi.',
            ]
        );

        $validated['notaris_id'] = auth()->user()->notaris_id;

        $this->service->store($validated);

        notyf()
            ->position('x', 'right')
            ->position('y', 'top')
            ->success('PIC Staff berhasil ditambahkan.');

        return redirect()->route($this->getIndexRoute());
    }

    public function edit(PicStaff $pic_staff)
    {
        $module = $this->getModule();

        return view(
            'pages.PIC.PicStaff.form',
            compact('pic_staff', 'module')
        );
    }

    public function update(Request $request, PicStaff $pic_staff)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone_number' => 'required|string|max:50',
            'position' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'note' => 'nullable|string|max:255',
        ]);

        $validated['notaris_id'] = auth()->user()->notaris_id;

        $this->service->update($pic_staff, $validated);

        notyf()
            ->position('x', 'right')
            ->position('y', 'top')
            ->success('PIC Staff berhasil diperbarui.');

        return redirect()->route($this->getIndexRoute());
    }

    public function destroy(PicStaff $pic_staff)
    {
        $this->service->destroy($pic_staff);

        notyf()
            ->position('x', 'right')
            ->position('y', 'top')
            ->success('PIC Staff berhasil dihapus.');

        return redirect()->route($this->getIndexRoute());
    }
}
