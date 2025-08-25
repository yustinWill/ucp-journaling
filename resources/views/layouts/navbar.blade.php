<!-- Navbar -->
<nav class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
            <i class="icon-base ti tabler-menu-2 icon-md"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
        <div class="navbar-nav align-items-center">
            <div class="col-12">
                <div class="nav-item navbar-search-wrapper mb-0">
                    <span id="company-badge-web" style="">
                        <span class="badge bg-primary">
                            Company Saat Ini: <b> {{ session('user_company_code') }} -
                                {{ session('user_company_name') }}</b>
                        </span>
                    </span>
                </div>
            </div>
        </div>

        <!-- /Search -->
        <ul class="navbar-nav flex-row align-items-center ms-md-auto">
            <li class="nav-item dropdown-language dropdown">
                <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill waves-effect"
                    href="javascript:void(0);" data-bs-toggle="dropdown">
                    <i class="icon-base ti tabler-building-cog icon-22px text-heading"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    @php
                        $companies = session('user_company', []);
                        $currentCompanyCode = session('user_company_code');
                    @endphp
                    @foreach ($companies as $company)
                        <li>
                            <a class="dropdown-item waves-effect {{ $company->company_code == $currentCompanyCode ? 'active' : '' }}"
                                href="{{ route('change_company.index', ['company_code' => $company->company_code]) }}"
                                data-company-code="{{ $company->company_code }}">
                                <span>{{ $company->company_code }} - {{ $company->company_name }}</span>
                                @if ($company->company_code == $currentCompanyCode)
                                    <i class="icon-base ti tabler-check icon-18px ms-2 text-success"></i>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
            <!--/ Language -->

            <!-- Style Switcher -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill waves-effect"
                    id="nav-theme" href="javascript:void(0);" data-bs-toggle="dropdown"
                    aria-label="Toggle theme (light)">
                    <i class="tabler-sun icon-base ti icon-22px theme-icon-active text-heading"></i>
                    <span class="d-none ms-2" id="nav-theme-text">Toggle theme</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="nav-theme-text">
                    <li>
                        <button type="button" class="dropdown-item align-items-center waves-effect active"
                            data-bs-theme-value="light" aria-pressed="true">
                            <span><i class="icon-base ti tabler-sun icon-22px me-3" data-icon="sun"></i>Light</span>
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item align-items-center waves-effect"
                            data-bs-theme-value="dark" aria-pressed="false">
                            <span><i class="icon-base ti tabler-moon-stars icon-22px me-3"
                                    data-icon="moon-stars"></i>Dark</span>
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item align-items-center waves-effect"
                            data-bs-theme-value="system" aria-pressed="false">
                            <span><i class="icon-base ti tabler-device-desktop-analytics icon-22px me-3"
                                    data-icon="device-desktop-analytics"></i>System</span>
                        </button>
                    </li>
                </ul>
            </li>
            <!-- / Style Switcher-->

            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <img src="../../assets/img/avatars/1.png" alt class="rounded-circle" />
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img src="../../assets/img/avatars/1.png" alt
                                            class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                                    <small class="text-body-secondary">{{ Auth::user()->role->name }}</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider my-1 mx-n2"></div>
                    </li>
                    {{-- <li>
                        <a class="dropdown-item" href="#">
                            <i class="icon-base ti tabler-user icon-md me-3"></i><span>My
                                Profile</span>
                        </a>
                    </li> --}}
                    <li>
                        <a class="dropdown-item" href="{{ route('change_password.index') }}">
                            <i class="icon-base ti tabler-lock icon-md me-3"></i><span>Change
                                Password</span>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider my-1 mx-n2"></div>
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="d-block">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="icon-base ti tabler-power icon-md me-3"></i><span>Log Out</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
            <!--/ User -->
        </ul>
    </div>
</nav>
