<?php

use App\Http\Controllers\AktaQrController;
use App\Http\Controllers\BackupRestoreController;
use App\Http\Controllers\ChangePassword;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentsController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\NotaryAktaDocumentsController;
use App\Http\Controllers\NotaryAktaLogsController;
use App\Http\Controllers\NotaryAktaPartiesController;
use App\Http\Controllers\NotaryAktaTransactionController;
use App\Http\Controllers\NotaryAktaTypesController;
use App\Http\Controllers\NotaryClientDocumentController;
use App\Http\Controllers\NotaryClientProductController;
use App\Http\Controllers\NotaryClientWarkahController;
use App\Http\Controllers\NotaryConsultationController;
use App\Http\Controllers\NotaryCostController;
use App\Http\Controllers\NotaryLaporanAktaController;
use App\Http\Controllers\NotaryLegalisasiController;
use App\Http\Controllers\NotaryLettersController;
use App\Http\Controllers\NotaryPaymenttController;
use App\Http\Controllers\NotaryRelaasAktaController;
use App\Http\Controllers\NotaryRelaasDocumentController;
use App\Http\Controllers\NotaryRelaasLogsController;
use App\Http\Controllers\NotaryRelaasPartiesController;
use App\Http\Controllers\NotaryWaarmerkingController;
use App\Http\Controllers\PicDocumentsController;
use App\Http\Controllers\PicHandOverController;
use App\Http\Controllers\PicProcessController;
use App\Http\Controllers\PicStaffController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProsesLainController;
use App\Http\Controllers\PublicPaymentController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RelaasTypeController;
use App\Http\Controllers\ReportPaymentController;
use App\Http\Controllers\ReportProcessController;
use App\Http\Controllers\ResetPassword;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SubscriptionsController;
use App\Http\Controllers\UserProfileController;
use App\Models\Notaris;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;

Route::middleware('guest', 'nocache')->group(function () {
    // LoginController routes
    Route::controller(LoginController::class)->group(function () {
        Route::get('/', 'show')->name('login');
        Route::post('/login', 'login')->name('login.perform');
        Route::get('/alert-forgot-password', 'alertForgotPassword')->name('alertForgotPassword');
        Route::get('/notaris/verify/{hash}', function ($hash) {
            try {
                $id = Crypt::decryptString($hash);
            } catch (\Exception $e) {
                abort(404);
            }

            $notaris = Notaris::with('user')->findOrFail($id);

            return view('pages.profile-notaris', compact('notaris'));
        })->name('profileNotaris');
    });

    // RegisterController routes
    Route::controller(RegisterController::class)->group(function () {
        Route::get('/register', 'create')->name('register');
        Route::post('/register', 'store')->name('register.perform');
    });
    // ResetPassword routes
    Route::controller(ResetPassword::class)->group(function () {
        Route::get('/reset-password', 'show')->name('reset-password');
        Route::post('/reset-password', 'send')->name('reset.perform');
    });
    // ChangePassword routes
    Route::controller(ChangePassword::class)->group(function () {
        Route::get('/change-password', 'show')->name('change-password');
        Route::post('/change-password', 'update')->name('change.perform');
    });
    Route::get('/public-client/{uuid}', [ClientController::class, 'showByUuid'])->name('clients.showByUuid');

    // Public Access
    // Route untuk akses form update dari link revisi (menggunakan encrypted id)
    // revisi / edit (link untuk revisi klien)
    Route::get('/client/revisi/{encryptedClientId}', [ClientController::class, 'editClient'])
        ->name('client.editClient');
    Route::put('/client/revisi/{encryptedClientId}', [ClientController::class, 'updateClient'])
        ->name('client.public.update');

    Route::get('/public/payment/{token}', [PublicPaymentController::class, 'show'])
        ->name('public.payment.show');

    // public form (link yang dikirim ke klien) — jelas beda URI
    Route::get('/client/public/{encryptedNotarisId}', [ClientController::class, 'publicForm'])
        ->name('client.public.create');

    Route::post('/client/public/{encryptedNotarisId}/store', [ClientController::class, 'storeClient'])
        ->name('client.public.store');

    // Detail Klien

    // Route::post('/client/search', [ClientController::class, 'searchByRegistrationCode'])->name('client.search');
    Route::post('/client/{uuid}/upload-document', [ClientController::class, 'uploadDocument'])
        ->name('client.uploadDocument');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings');

    Route::post('/profile/unlock', [UserProfileController::class, 'unlock'])
        ->name('profile.unlock');
});

Route::middleware(['auth', 'check.full.access'])->group(function () {

    // Route::middleware(['auth'])->group(function () {
    // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Route::controller(UserProfileController::class)->group(function () {
    //     Route::get('/profile', 'show')->name('profile');
    //     Route::put('/profile', 'update')->name('profile.update');
    // });

    Route::controller(UserProfileController::class)->group(function () {
        Route::get('/profile', 'show')->name('profile');
        Route::put('/profile', 'update')->name('profile.update');
    });

    Route::get('/backup-restore', [BackupRestoreController::class, 'index'])->name('backup-restore.index');

    Route::post('/backup', [BackupRestoreController::class, 'backup'])->name('backup');
    Route::post('/restore', [BackupRestoreController::class, 'restore'])->name('restore');
    Route::get('/akta/{transaction_code}', [AktaQrController::class, 'show'])
        ->name('akta.qr.show');

    Route::resource('consultation', NotaryConsultationController::class);
    Route::get('/consultation/client/{id}', [NotaryConsultationController::class, 'getConsultationByClient'])->name('consultation.getConsultationByClient');
    Route::get('/consultation/client/product/{consultationId}', [NotaryConsultationController::class, 'getConsultationByProduct'])->name('consultation.detail');
    Route::get('/consultation/client/product/creates/{consultationId}', [NotaryConsultationController::class, 'creates'])->name('consultation.creates');
    Route::post('/consultation/client/product/{consultationId}', [NotaryConsultationController::class, 'storeProduct'])->name('consultation.storeProduct');
    Route::delete('/consultation/client/product/{consultationId}/product/{productId}', [NotaryConsultationController::class, 'deleteProduct'])->name('consultation.deleteProduct');

    // UserProfileController routes

    Route::controller(SubscriptionsController::class)->group(function () {
        Route::get('/subscriptions', 'index')->name('subscriptions');
    });

    Route::resource('products', ProductsController::class)->except('show');
    Route::put('products/{product}/deactivate', [ProductsController::class, 'deactivate'])->name('products.deactivate');
    Route::resource('documents', DocumentsController::class)->except('show');
    Route::put('documents/{document}/deactivate', [DocumentsController::class, 'deactivate'])->name('documents.deactivate');
    Route::put('/documents/{id}/activate', [DocumentsController::class, 'activate'])
        ->name('documents.activate');

    Route::resource('clients', ClientController::class)->except('show');
    Route::put('/clients/{id}/valid', [ClientController::class, 'markAsValid'])->name('clients.markAsValid');
    Route::put('/clients/{id}/set-revision', [ClientController::class, 'setRevision'])
        ->name('clients.setRevision');
    Route::get('/client/revision/{encryptedClientId}', [ClientController::class, 'showRevisionForm'])
        ->name('client.public.revision');

    Route::post('/client/revision/{encryptedClientId}', [ClientController::class, 'submitRevision'])
        ->name('client.revision.submit');

    // Proses Pengurusan
    Route::get('client-progress', [PicProcessController::class, 'indexProcess'])->name('pic-progress.indexProcess');
    Route::post('pic_process/progress/store', [PicProcessController::class, 'storeProgress'])
        ->name('pic-progress.storeProgress');
    Route::resource('management-process', NotaryClientProductController::class);
    Route::put('/management-process/{id}/valid', [NotaryClientProductController::class, 'markAsValid'])->name('management-process.markAsValid');
    Route::post('management-process/mark-done', [NotaryClientProductController::class, 'markDone'])->name('management-process.markDone');
    Route::post('management-process/add-progress', [NotaryClientProductController::class, 'addProgress'])->name('management-process.addProgress');
    // BackOffice Dokumen
    Route::resource('management-document', NotaryClientDocumentController::class);
    // Route::get('management-document', [NotaryClientDocumentController::class, 'index'])->name('management-document.index');
    Route::get('management-document/create', [NotaryClientDocumentController::class, 'create'])->name('management-document.create');
    Route::post('management-document/store', [NotaryClientDocumentController::class, 'addDocument'])->name('management-document.addDocument');
    Route::post('management-document/mark-done', [NotaryClientDocumentController::class, 'markDone'])->name(
        'management-document.markDone'
    );
    Route::post('management-document/status', [NotaryClientDocumentController::class, 'updateStatus'])->name('management-document.updateStatus');

    // Warkah
    Route::get('/warkah', [NotaryClientWarkahController::class, 'selectClient'])->name('warkah.selectClient');
    Route::get('/warkah/{id}', [NotaryClientWarkahController::class, 'index'])->name('warkah.index');
    // Route::resource('warkah', NotaryClientWarkahController::class);

    Route::post('warkah/store', [NotaryClientWarkahController::class, 'store'])->name('warkah.store');
    Route::get('warkah/create/{id}', [NotaryClientWarkahController::class, 'create'])->name('warkah.create');
    Route::put('warkah/update/{id}', [NotaryClientWarkahController::class, 'update'])->name('warkah.update');
    // Route::post('warkah/add-document/{id}', [NotaryClientWarkahController::class, 'addDocument'])->name('warkah.addDocument');
    Route::post('warkah/status/{id}', [NotaryClientWarkahController::class, 'updateStatus'])->name('warkah.updateStatus');
    // // End
    // Partij Akta / Akta Transaksi
    Route::resource('akta-types', NotaryAktaTypesController::class);
    Route::get('akta-transactions/select-client', [NotaryAktaTransactionController::class, 'selectClient'])
        ->name('akta-transactions.selectClient');
    Route::resource('akta-transactions', NotaryAktaTransactionController::class);
    Route::resource('akta-documents', NotaryAktaDocumentsController::class);

    Route::get('/akta-documents/create/{akta_transaction_id}', [NotaryAktaDocumentsController::class, 'createData'])
        ->name('akta-documents.createData');

    Route::post('/akta-documents/store/{akta_transaction_id}', [NotaryAktaDocumentsController::class, 'storeData'])
        ->name('akta-documents.storeData');
    Route::resource('akta-parties', NotaryAktaPartiesController::class)->except('create', 'store', 'show');
    Route::get('akta-parties/createData/{akta_transaction_id}', [NotaryAktaPartiesController::class, 'createData'])
        ->name('akta-parties.createData');
    Route::post('/akta-parties,store/{akta_transaction_id}', [NotaryAktaPartiesController::class, 'storeData'])->name('akta-parties.storeData');
    Route::get('akta-number', [NotaryAktaTransactionController::class, 'indexNumber'])->name('akta_number.index');
    Route::post('akta-number/store', [NotaryAktaTransactionController::class, 'storeNumber'])->name(
        'akta_number.store'
    );
    Route::resource('akta-logs', NotaryAktaLogsController::class);

    // Relaas Akta / PPAT
    Route::resource('relaas-types', RelaasTypeController::class);
    Route::get('relaas-aktas/select-client', [NotaryRelaasAktaController::class, 'selectClient'])
        ->name('relaas-aktas.selectClient');
    Route::resource('relaas-aktas', NotaryRelaasAktaController::class);
    Route::resource('relaas-parties', NotaryRelaasPartiesController::class);
    Route::get('/relaas-parties/createData/{relaas_id}', [NotaryRelaasPartiesController::class, 'create'])->name('relaas-parties.create');
    Route::post('/relaas-parties/store/{relaas_id}', [NotaryRelaasPartiesController::class, 'store'])->name('relaas-parties.store');
    Route::get('/relaas-parties/edit/{relaas_id}/{id}', [NotaryRelaasPartiesController::class, 'edit'])->name('relaas-parties.edit');
    Route::put('/relaas-parties/update/{relaas_id}/{id}', [NotaryRelaasPartiesController::class, 'update'])->name('relaas-parties.update');
    Route::get('/relaas-number/number_akta', [NotaryRelaasAktaController::class, 'indexNumber'])->name('relaas_akta.indexNumber');
    Route::post('/relaas-akta/store', [NotaryRelaasAktaController::class, 'storeNumber'])->name(
        'relaas-akta.store'
    );
    Route::resource('relaas-documents', NotaryRelaasDocumentController::class);
    Route::get('/relaas-documents/create/{relaas_id}', [NotaryRelaasDocumentController::class, 'create'])->name('relaas-documents.create');
    Route::post('/relaas-documents/store/{relaas_id}', [NotaryRelaasDocumentController::class, 'store'])->name('relaas-documents.store');
    Route::get('/relaas-documents/edit/{relaas_id}/{id}', [NotaryRelaasDocumentController::class, 'edit'])->name('relaas-documents.edit');
    Route::put('/relaas-documents/update/{relaas_id}/{id}', [NotaryRelaasDocumentController::class, 'update'])->name('relaas-documents.update');
    Route::resource('relaas-logs', NotaryRelaasLogsController::class);

    Route::resource('notary-legalisasi', NotaryLegalisasiController::class);
    Route::resource('notary-waarmerking', NotaryWaarmerkingController::class);
    Route::resource('notary-letters', NotaryLettersController::class);
    Route::get('laporan-akta', [NotaryLaporanAktaController::class, 'index'])->name('laporan-akta.index');
    Route::get('laporan-akta/export-pdf', [NotaryLaporanAktaController::class, 'exportPdf'])
        ->name('laporan-akta.export-pdf');

    // PIC
    Route::resource('pic_documents', PicDocumentsController::class);
    Route::get('pic_documents/{id}/print', [PicDocumentsController::class, 'print'])->name('pic_documents.print');
    Route::resource('pic_staff', PicStaffController::class);
    Route::resource('pic_process', PicProcessController::class);
    Route::put('/pic_process/{id}/complete', [PicProcessController::class, 'markComplete'])->name('pic_process.markComplete');
    Route::resource('pic_handovers', PicHandOverController::class);
    Route::get('pic_handovers/{id}/print', [PicHandoverController::class, 'print'])->name('pic_handovers.print');

    // Biaya
    Route::resource('notary_costs', NotaryCostController::class);
    Route::get('notary_costs/{id}/print', [NotaryCostController::class, 'print'])->name('notary_costs.print');
    Route::resource('notary_payments', NotaryPaymenttController::class);
    Route::get('notary_payments/{id}/print', [NotaryPaymenttController::class, 'print'])->name('notary_payments.print');
    Route::PATCH('notary_payments/{id}/valid', [NotaryPaymenttController::class, 'valid'])->name('notary_payments.valid');
    Route::get('report-payment', [ReportPaymentController::class, 'index'])->name('report-payment.index');
    Route::get('report-payment/print', [ReportPaymentController::class, 'print'])->name('report-payment.print');
    Route::get('report-progress', [ReportProcessController::class, 'index'])->name('report-progress.index');
    Route::get('report-progress/print', [ReportProcessController::class, 'print'])->name('report-progress.print');
    // Logout route
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    // Proses Lain
    Route::resource('proses-lain-transaksi', ProsesLainController::class);
    Route::get('proses-lain-pic', [ProsesLainController::class, 'indexPic'])->name('proses-lain-pic.index');
    Route::get('proses-lain-pic/create', [ProsesLainController::class, 'createPic'])->name('proses-lain-pic.create');
    Route::get('proses-lain-pic/{id}', [ProsesLainController::class, 'showPic'])->name('proses-lain-pic.show');
    Route::post('proses-lain-pic', [ProsesLainController::class, 'storePic'])->name('proses-lain-pic.store');
    Route::put('proses-lain-pic/{id}', [ProsesLainController::class, 'updatePic'])->name('proses-lain-pic.update');
    Route::get('proses-lain-progress', [ProsesLainController::class, 'indexProgress'])->name('proses-lain-progress.index');
    Route::post('proses-lain-progress/store', [ProsesLainController::class, 'storeProgress'])->name('proses-lain-progress.store');
    Route::put('proses-lain-progress/{id}', [ProsesLainController::class, 'updateProgress'])->name('proses-lain-progress.update');
    Route::delete('proses-lain-progress/{id}', [ProsesLainController::class, 'destroyProgress'])->name('proses-lain-progress.destroy');
});
