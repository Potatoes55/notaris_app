<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\UserObserver;
use App\Repositories\ClientRepository;
use App\Repositories\DocumentRepository;
use App\Repositories\Interfaces\ClientRepositoryInterface;
use App\Repositories\Interfaces\DocumentRepositoryInterface;
use App\Repositories\Interfaces\NotaryAktaDocumentRepositoryInterface;
use App\Repositories\Interfaces\NotaryAktaLogRepositoryInterface;
use App\Repositories\Interfaces\NotaryAktaPartiesRepositoryInterface;
use App\Repositories\Interfaces\NotaryAktaTransactionRepositoryInterface;
use App\Repositories\Interfaces\NotaryAktaTypeRepositoryInterface;
use App\Repositories\Interfaces\NotaryClientDocumentRepositoryInterface;
use App\Repositories\Interfaces\NotaryClientProductRepositoryInterface;
use App\Repositories\Interfaces\NotaryClientProgressRepositoryInterface;
use App\Repositories\Interfaces\NotaryClientWarkahRepositoryInterface;
use App\Repositories\Interfaces\NotaryConsultationServiceInterface;
use App\Repositories\Interfaces\NotaryCostRepositoryInterface;
use App\Repositories\Interfaces\NotaryLegalisasiRepositoryInterface;
use App\Repositories\Interfaces\NotaryLetterRepositoryInterface;
use App\Repositories\Interfaces\NotaryRelaasAktaRepositoryInterface;
use App\Repositories\Interfaces\NotaryRelaasLogsRepositoryInterface;
use App\Repositories\Interfaces\PicDocumentsRepositoryInterface;
use App\Repositories\Interfaces\PicHandoverRepositoryInterface;
use App\Repositories\Interfaces\PicProcessRepositoryInterface;
use App\Repositories\Interfaces\PicStaffRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\RelaasPartiesRepositoryInterface;
use App\Repositories\Interfaces\WaarmerkingRepositoryInterface;
use App\Repositories\NotaryAktaDocumentRepository;
use App\Repositories\NotaryAktaLogRepository;
use App\Repositories\NotaryAktaPartiesRepository;
use App\Repositories\NotaryAktaTransactionRepository;
use App\Repositories\NotaryAktaTypeRepository;
use App\Repositories\NotaryClientDocumentRepository;
use App\Repositories\NotaryClientProductRepository;
use App\Repositories\NotaryClientProgressRepository;
use App\Repositories\NotaryClientWarkahRepository;
use App\Repositories\NotaryConsultationRepository;
use App\Repositories\NotaryCostRepository;
use App\Repositories\NotaryLegalisasiRepository;
use App\Repositories\NotaryLetterRepository;
use App\Repositories\NotaryRelaasAktaRepository;
use App\Repositories\NotaryRelaasLogsRepository;
use App\Repositories\PicDocumentsRepository;
use App\Repositories\PicHandoverRepository;
use App\Repositories\PicProcessRepository;
use App\Repositories\PicStaffRepository;
use App\Repositories\ProductRepository;
use App\Repositories\RelaasPartiesRepository;
use App\Repositories\WaarmerkingRepository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Email;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(DocumentRepositoryInterface::class, DocumentRepository::class);
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
        $this->app->bind(NotaryConsultationServiceInterface::class, NotaryConsultationRepository::class);
        $this->app->bind(NotaryClientProductRepositoryInterface::class, NotaryClientProductRepository::class);
        $this->app->bind(NotaryClientProgressRepositoryInterface::class, NotaryClientProgressRepository::class);
        $this->app->bind(NotaryClientDocumentRepositoryInterface::class, NotaryClientDocumentRepository::class);
        $this->app->bind(NotaryClientWarkahRepositoryInterface::class, NotaryClientWarkahRepository::class);
        $this->app->bind(NotaryAktaTypeRepositoryInterface::class, NotaryAktaTypeRepository::class);
        $this->app->bind(NotaryAktaTransactionRepositoryInterface::class, NotaryAktaTransactionRepository::class);
        $this->app->bind(NotaryAktaDocumentRepositoryInterface::class, NotaryAktaDocumentRepository::class);
        $this->app->bind(NotaryAktaPartiesRepositoryInterface::class, NotaryAktaPartiesRepository::class);
        $this->app->bind(NotaryAktaLogRepositoryInterface::class, NotaryAktaLogRepository::class);
        $this->app->bind(NotaryRelaasAktaRepositoryInterface::class, NotaryRelaasAktaRepository::class);
        $this->app->bind(NotaryRelaasLogsRepositoryInterface::class, NotaryRelaasLogsRepository::class);
        $this->app->bind(RelaasPartiesRepositoryInterface::class, RelaasPartiesRepository::class);
        $this->app->bind(NotaryLegalisasiRepositoryInterface::class, NotaryLegalisasiRepository::class);
        $this->app->bind(WaarmerkingRepositoryInterface::class, WaarmerkingRepository::class);
        $this->app->bind(NotaryLetterRepositoryInterface::class, NotaryLetterRepository::class);
        $this->app->bind(PicStaffRepositoryInterface::class, PicStaffRepository::class);
        $this->app->bind(PicDocumentsRepositoryInterface::class, PicDocumentsRepository::class);
        $this->app->bind(PicProcessRepositoryInterface::class, PicProcessRepository::class);
        $this->app->bind(PicHandoverRepositoryInterface::class, PicHandoverRepository::class);
        $this->app->bind(NotaryCostRepositoryInterface::class, NotaryCostRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        Paginator::useBootstrapFive();

        Email::defaults(function () {
            return Email::strict();
        });
        View::composer('*', function ($view) {

            if (Request::is('ppat*')) {
                $module = 'PPAT';
            } elseif (Request::is('proses-lain*')) {
                $module = 'Proses Lain';
            } elseif (Request::is('konsultasi*')) {
                $module = 'Konsultasi';
            } else {
                $module = 'Notaris';
            }

            $view->with('module', $module);
        });
    }
}
