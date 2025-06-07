<!doctype html>

<html
  lang="en"
  class="layout-navbar-fixed layout-menu-fixed layout-compact"
  dir="ltr"
  data-skin="default"
  data-assets-path="../../assets/"
  data-template="vertical-menu-template-starter"
  data-bs-theme="light">
  @include('layouts.header')

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->

        @include('layouts.menu')

        <div class="menu-mobile-toggler d-xl-none rounded-1">
          <a href="javascript:void(0);"
            class="layout-menu-toggle menu-link text-large text-bg-secondary p-2 rounded-1">
            <i class="ti tabler-menu icon-base"></i>
            <i class="ti tabler-chevron-right icon-base"></i>
          </a>
        </div>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          @include('layouts.navbar')

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->
            <div class="container-xxl flex-grow-1 container-p-y">
              <h4 class="py-4 mb-6">Page 1</h4>
              <p>
                Sample page.<br />For more layout options use

                <a
                  href="https://demos.pixinvent.com/vuexy-html-admin-template/documentation//layouts.html"
                  target="_blank"
                  class="fw-medium">Layout docs</a>.
              </p>
            </div>
            <!-- / Content -->

            @include('layouts.footer')

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>

      <!-- Drag Target Area To SlideIn Menu On Small Screens -->
      <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    @include('layouts.script')
  </body>
  @include('layouts.script')
  @include('layouts.global_script')
  @yield('ext_js_script')
  @yield('js_script')
</html>
