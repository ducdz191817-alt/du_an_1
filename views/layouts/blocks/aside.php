<!--begin::Sidebar-->
<aside class="app-sidebar sidebar-expand-lg sidebar-dark-primary">
  <!--begin::Sidebar Brand-->
  <div class="sidebar-brand">
    <a href="<?= BASE_URL ?>home" class="brand-link">
      <img
        src="<?= asset('dist/assets/img/AdminLTELogo.png') ?>"
        alt="AdminLTE Logo"
        class="brand-image opacity-75 shadow"
      />
      <span class="brand-text fw-light">AdminLTE 4</span>
    </a>
  </div>
  <!--end::Sidebar Brand-->
  <!--begin::Sidebar Wrapper-->
  <div class="sidebar-wrapper">
    <!--begin::Sidebar Menu-->
    <nav class="mt-2">
      <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
        <!--begin::Dashboard-->
        <li class="nav-item">
          <a href="<?= BASE_URL ?>?act=dashboard" class="nav-link">
            <i class="nav-icon bi bi-speedometer2"></i>
            <p>Dashboard</p>
          </a>
        </li>
        <!--end::Dashboard-->
        
        <!--begin::Quản lý Tour-->
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon bi bi-airplane-engines"></i>
            <p>
              Quản lý Tour
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="<?= BASE_URL ?>?act=categories" class="nav-link">
                <i class="nav-icon bi bi-tags"></i>
                <p>Danh mục Tour</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?= BASE_URL ?>?act=tours" class="nav-link">
                <i class="nav-icon bi bi-list-ul"></i>
                <p>Danh sách Tour</p>
              </a>
            </li>
          </ul>
        </li>
        <!--end::Quản lý Tour-->
        
        <!--begin::Quản lý Booking-->
        <li class="nav-item">
          <a href="<?= BASE_URL ?>?act=bookings" class="nav-link">
            <i class="nav-icon bi bi-calendar-check"></i>
            <p>Quản lý Booking</p>
          </a>
        </li>
        <!--end::Quản lý Booking-->
        
        <!--begin::Quản lý Khách hàng-->
        <li class="nav-item">
          <a href="<?= BASE_URL ?>?act=customers" class="nav-link">
            <i class="nav-icon bi bi-people"></i>
            <p>Quản lý Khách hàng</p>
          </a>
        </li>
        <!--end::Quản lý Khách hàng-->
        
        <!--begin::Quản lý Hướng dẫn viên-->
        <li class="nav-item">
          <a href="<?= BASE_URL ?>?act=guides" class="nav-link">
            <i class="nav-icon bi bi-person-badge"></i>
            <p>Quản lý Hướng dẫn viên</p>
          </a>
        </li>
        <!--end::Quản lý Hướng dẫn viên-->
        
        <!--begin::Quản lý Người dùng-->
        <li class="nav-item">
          <a href="<?= BASE_URL ?>?act=users" class="nav-link">
            <i class="nav-icon bi bi-person-gear"></i>
            <p>Quản lý Người dùng</p>
          </a>
        </li>
        <!--end::Quản lý Người dùng-->
      </ul>
    </nav>
    <!--end::Sidebar Menu-->
  </div>
  <!--end::Sidebar Wrapper-->
</aside>
<!--end::Sidebar-->
