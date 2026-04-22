<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur"
    data-scroll="false">
    <div class="container-fluid py-1 px-3" style="flex-wrap: nowrap !important">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm">
                    <a class="opacity-5 text-white" href="javascript:;">Halaman</a>
                </li>
                <li class="breadcrumb-item text-sm text-white active" aria-current="page">{{ $title }}</li>
            </ol>
            <h6 class="font-weight-bolder text-white mb-0">{{ $title }}</h6>
        </nav>

        <div class="collapse navbar-collapse d-none d-xl-flex mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                {{-- <div class="input-group">
                    <span class="input-group-text text-body">
                        <i class="fas fa-search" aria-hidden="true"></i>
                    </span>
                    <input type="text" class="form-control" placeholder="Type here...">
                </div> --}}
            </div>
            <ul class="navbar-nav justify-content-end">
                <li class="nav-item d-flex align-items-center">
                    <form method="post" action="{{ route('logout') }}" id="logout-form">
                        @csrf
                        <button type="submit" class="btn btn-white text-primary mb-0 shadow">
                            <i class="fa fa-user me-sm-1"></i>
                            <span class="d-inline d-sm-inline d-md-inline">Keluar</span>
                        </button>
                    </form>
                </li>
            </ul>
        </div>

        <!-- Toggle sidenav: hanya tampil <1200px -->
        <ul class="navbar-nav ms-auto d-flex d-none flex-row">
            <li class="nav-item d-flex align-items-center">
                <form method="post" action="{{ route('logout') }}" id="logout-form">
                    @csrf
                    <button type="submit" class="btn btn-white text-primary mb-0 shadow">
                        <i class="fa fa-user me-lg-1"></i>
                        <span class="d-sm-inline  text-wrap">Keluar</span>
                    </button>
                </form>
            </li>
            <li class="nav-item ps-3 d-flex align-items-center">
                <a href="javascript:;" class="nav-link text-white p-0" id="iconNavbarSidenav">
                    <div class="sidenav-toggler-inner">
                        <i class="sidenav-toggler-line bg-white"></i>
                        <i class="sidenav-toggler-line bg-white"></i>
                        <i class="sidenav-toggler-line bg-white"></i>
                    </div>
                </a>
            </li>
        </ul>
    </div>
</nav>
<!-- End Navbar -->
