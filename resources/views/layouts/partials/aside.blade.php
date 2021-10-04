<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="index3.html" class="brand-link">
    <img src="{{ asset('backend/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo"
      class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-light">My Account Version 0.1</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-1 mb-1 d-flex">
      <div class="image"></div>
      <div class="info">
        <a href="#" class="d-block">Login : Admin</a>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- ลูกหนี้/เจ้าหนี้ -->
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon far fa-address-book"></i>
            <p>
              ลูกหนี้/เจ้าหนี้
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">            
            <li class="nav-item">
              <a href="{{ route('accstar.customer') }}"
                class="nav-link {{ request()->is('accstar/customer') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>ลูกค้า/ผู้ขาย</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>ลูกหนี้</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>เจ้าหนี้</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- ระบบสินค้า -->
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fab fa-product-hunt"></i>
            <p>
              ระบบสินค้า
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">            
            <li class="nav-item">
              <a href="{{ route('accstar.inventory') }}"
                class="nav-link {{ request()->is('accstar/inventory') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon" aria-hidden="true"></i>
                <p>ข้อมูลสินค้า</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>ปรับปรุงสินค้า</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>อายุสินค้า</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- ระบบจัดซื้อ -->
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-shopping-basket"></i>
            <p>
              ระบบจัดซื้อ
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">            
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon" aria-hidden="true"></i>
                <p>รับสินค้าพร้อมใบกำกับ</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>ใบสั่งซื้อ</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>รับสินค้าเท่านั้น</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>ใบกำกับของสินค้าที่รับแล้ว</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- ระบบขาย -->
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-shipping-fast"></i>
            <p>
              ระบบขาย
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">            
            <li class="nav-item">
              <a href="{{ route('accstar.sodeliverytax') }}"
                class="nav-link {{ request()->is('accstar/sodeliverytax') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>ส่งสินค้าพร้อมใบกำกับ</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>ใบสั่งขาย</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>ส่งสินค้าเท่านั้น</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>ใบกำกับของสินค้าที่ส่งแล้ว</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- ระบบรับชำระเงิน -->
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-hand-holding-usd"></i>
            <p>
              ระบบรับชำระเงิน
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">            
            <li class="nav-item">
              <a href="{{ route('accstar.receiveonsales') }}"
                class="nav-link {{ request()->is('accstar/receiveonsales') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i></i>
                <p>รับชำระเงิน</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>ใบแจ้งหนี้/ใบวางบิล</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- ระบบจ่ายชำระเงิน -->
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fab fa-amazon-pay"></i>
            <p>
              ระบบจ่ายชำระเงิน
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">            
            <li class="nav-item">
              <a href="{{ route('accstar.receiveonsales') }}"
                class="nav-link {{ request()->is('accstar/receiveonsales') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i></i>
                <p>จ่ายชำระเงิน</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>รับใบแจ้งหนี้</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- ระบบบัญชีทั่วไป -->
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-table"></i>
            <p>
              ระบบบัญชีทั่วไป
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">            
            <li class="nav-item">
              <a href="{{ route('accstar.gljournal') }}"
                class="nav-link {{ request()->is('accstar/gjournal') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>ใบสำคัญ</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('accstar.trialbalance') }}"
                class="nav-link {{ request()->is('accstar/trialbalance') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon" aria-hidden="true"></i>
                <p>งบทดลอง</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('accstar.postjournal') }}"
                class="nav-link {{ request()->is('accstar/postjournal') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon" aria-hidden="true"></i>
                <p>ผ่านรายการบัญชี</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('accstar.cancelpostjournal') }}"
                class="nav-link {{ request()->is('accstar/cancelpostjournal') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon" aria-hidden="true"></i>
                <p>ยกเลิกผ่านรายการบัญชี</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('accstar.listcoa') }}"
                class="nav-link {{ request()->is('accstar/listcoa') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon" aria-hidden="true"></i>
                <p>ผังบัญชี</p>
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>