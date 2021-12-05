<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <div class="brand-link">
    <img src="{{ asset('backend/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo"
      class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text" style="font-size: 15px;">My Account Version 0.1</span>
  </div>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-1 mb-1 d-flex">
      <div class="image"></div>
      <div class="info">
        <a href="#" class="d-block">Logout : Admin</a>
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
              <a href="{{ route('accstar.customer.customer') }}"
                class="nav-link {{ request()->is('accstar/customer/customer') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon-submenu"></i>
                <p>ลูกค้า/ผู้ขาย</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon-submenu"></i>
                <p>_ลูกหนี้</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon-submenu"></i>
                <p>_เจ้าหนี้</p>
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
              <a href="{{ route('accstar.inventory.inventory') }}"
                class="nav-link {{ request()->is('accstar/inventory/inventory') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon-submenu" aria-hidden="true"></i>
                <p>ข้อมูลสินค้า</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('accstar.inventory.inventoryserialno') }}"
                class="nav-link {{ request()->is('accstar/inventory/inventoryserialno') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon-submenu" aria-hidden="true"></i>
                <p>ข้อมูลสินค้า Serial No</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('accstar.inventory.adjustinventory') }}" 
                class="nav-link {{ request()->is('accstar/inventory/adjustinventory') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon-submenu"></i>
                <p>ปรับปรุงสินค้า</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon-submenu"></i>
                <p>_อายุสินค้า</p>
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
                <i class="far fa-circle nav-icon-submenu" aria-hidden="true"></i>
                <p>_รับสินค้าพร้อมใบกำกับ</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon-submenu"></i>
                <p>_ใบสั่งซื้อ</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon-submenu"></i>
                <p>_รับสินค้าเท่านั้น</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon-submenu"></i>
                <p>_ใบกำกับของสินค้าที่รับแล้ว</p>
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
              <a href="{{ route('accstar.sales.sodeliverytax') }}"
                class="nav-link {{ request()->is('accstar/sales/sodeliverytax') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon-submenu"></i>
                <p>ส่งสินค้าพร้อมใบกำกับ</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('accstar.sales.salesorder') }}"
                class="nav-link {{ request()->is('accstar/sales/salesorder') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon-submenu"></i>
                <p>ใบสั่งขาย</p>
              </a>
            </li>            
            <li class="nav-item">
              <a href="{{ route('accstar.sales.sodelivery') }}"
                class="nav-link {{ request()->is('accstar/sales/sodelivery') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon-submenu"></i>
                <p>ส่งสินค้าเท่านั้น</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('accstar.sales.sotax') }}"
                class="nav-link {{ request()->is('accstar/sales/sotax') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon-submenu"></i>
                <p>ใบกำกับของสินค้าที่ส่งแล้ว</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('accstar.sales.adjustsodeliverytax') }}"
                class="nav-link {{ request()->is('accstar/sales/adjustsodeliverytax') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon-submenu"></i>
                <p>ปรับปรุงราคาขาย</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('accstar.sales.returngoods') }}"
                class="nav-link {{ request()->is('accstar/sales/returngoods') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon-submenu"></i>
                <p>รับคืนสินค้า</p>
              </a>
            </li>
            {{-- <hr style="height:2px;border-width:0;color:gray;background-color:gray"> --}}
            <li class="nav-item">
              <a href="{{ route('accstar.sales.cancelsodeliverytax') }}"
                class="nav-link {{ request()->is('accstar/sales/cancelsodeliverytax') ? 'active' : '' }}">
                <i class="nav-icon-submenu fas fa-times"></i>
                <p>ยกเลิก-ส่งสินค้าพร้อมใบกำกับ</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('accstar.sales.cancelsalesorder') }}"
                class="nav-link {{ request()->is('accstar/sales/cancelsalesorder') ? 'active' : '' }}">
                <i class="nav-icon-submenu fas fa-times"></i>
                <p>ยกเลิก-ใบสั่งขาย</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('accstar.sales.cancelsodelivery') }}"
                class="nav-link {{ request()->is('accstar/sales/cancelsodelivery') ? 'active' : '' }}">
                <i class="nav-icon-submenu fas fa-times"></i>
                <p>ยกเลิก-ใบส่งสินค้า</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('accstar.sales.cancelsotax') }}"
                class="nav-link {{ request()->is('accstar/sales/cancelsotax') ? 'active' : '' }}">
                <i class="nav-icon-submenu fas fa-times"></i>
                <p>ยกเลิก-ใบกำกับของสินค้าที่ส่งแล้ว</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('accstar.sales.canceladjustsodeliverytax') }}"
                class="nav-link {{ request()->is('accstar/sales/canceladjustsodeliverytax') ? 'active' : '' }}">
                <i class="nav-icon-submenu fas fa-times"></i>
                <p>ยกเลิก-ปรับปรุงราคาขาย</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('accstar.sales.cancelreturngoods') }}"
                class="nav-link {{ request()->is('accstar/sales/cancelreturngoods') ? 'active' : '' }}">
                <i class="nav-icon-submenu fas fa-times"></i>
                <p>ยกเลิก-รับคืนสินค้า</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('accstar.sales.saleshistory') }}"
                class="nav-link {{ request()->is('accstar/sales/saleshistory') ? 'active' : '' }}">
                <i class="nav-icon-submenu fas fa-search"></i>
                <p>ประวัติการขาย</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('accstar.sales.salesbackorder') }}"
                class="nav-link {{ request()->is('accstar/sales/salesbackorder') ? 'active' : '' }}">
                <i class="nav-icon-submenu fas fa-search"></i>
                <p>รายการค้างส่ง</p>
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
              <a href="{{ route('accstar.finance.receiveonsales') }}"
                class="nav-link {{ request()->is('accstar/finance/receiveonsales') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon-submenu"></i></i>
                <p>รับชำระเงินตามใบเรียกเก็บเงิน</p>
              </a>
            </li>
            <li class="nav-item"> 
              <a href="{{ route('accstar.finance.billingnotice') }}" 
              class="nav-link {{ request()->is('accstar/finance/billingnotice') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon-submenu"></i>
                <p>ใบแจ้งหนี้/ใบวางบิล</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('accstar.finance.cancelreceiveonsales') }}"
                class="nav-link {{ request()->is('accstar/finance/cancelreceiveonsales') ? 'active' : '' }}">
                <i class="fas fa-times nav-icon-submenu" aria-hidden="true"></i>
                <p>ยกเลิก-รับชำระเงินตามใบเรียกฯ</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('accstar.finance.receivehistory') }}"
                class="nav-link {{ request()->is('accstar/finance/receivehistory') ? 'active' : '' }}">
                <i class="nav-icon-submenu fas fa-search"></i>
                <p>ประวัติการรับชำระ</p>
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
              <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon-submenu"></i>
                <p>_จ่ายชำระเงิน</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon-submenu"></i>
                <p>_รับใบแจ้งหนี้</p>
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
              <a href="{{ route('accstar.account.gljournal') }}"
                class="nav-link {{ request()->is('accstar/account/gjournal') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon-submenu"></i>
                <p>ใบสำคัญ</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('accstar.account.trialbalance') }}"
                class="nav-link {{ request()->is('accstar/account/trialbalance') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon-submenu" aria-hidden="true"></i>
                <p>งบทดลอง</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('accstar.account.postjournal') }}"
                class="nav-link {{ request()->is('accstar/account/postjournal') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon-submenu" aria-hidden="true"></i>
                <p>ผ่านรายการบัญชี</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('accstar.account.cancelpostjournal') }}"
                class="nav-link {{ request()->is('accstar/account/cancelpostjournal') ? 'active' : '' }}">
                <i class="fas fa-times nav-icon-submenu" aria-hidden="true"></i>
                <p>ยกเลิก-ผ่านรายการบัญชี</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('accstar.account.listcoa') }}"
                class="nav-link {{ request()->is('accstar/account/listcoa') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon-submenu" aria-hidden="true"></i>
                <p>ผังบัญชี</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- ระบบภาษี -->
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-coins"></i>
            <p>
              ระบบภาษี
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">            
            <li class="nav-item">
              <a href="{{ route('accstar.tax.salestax') }}"
                class="nav-link {{ request()->is('accstar/tax/salestax') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon-submenu"></i>
                <p>ภาษีขาย</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('accstar.tax.purchasetax') }}"
                class="nav-link {{ request()->is('accstar/tax/purchasetax') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon-submenu"></i>
                <p>ภาษีซื้อ</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('accstar.tax.withholdingtax') }}"
                class="nav-link {{ request()->is('accstar/tax/withholdingtax') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon-submenu"></i>
                <p>ภาษีหัก ณ ที่จ่าย</p>
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

@push("js")
<script type="text/javascript">
  $(function () {
      var params = window.location.pathname;
      params = params.toLowerCase();

      if (params != "/") {
          $(".nav-sidebar li a").each(function (i) {
              var obj = this;
              var url = $(this).attr("href");
              if (url == "" || url == "#") {
                  return true;
              }
              url = url.toLowerCase();
              if (url.indexOf(params) > -1) {
                  $(this).parent().addClass("active open menu-open");
                  $(this).parent().parent().addClass("active open menu-open");
                  $(this).parent().parent().parent().addClass("active open menu-open");
                  $(this).parent().parent().parent().parent().addClass("active open menu-open");
                  $(this).parent().parent().parent().parent().parent().addClass("active open menu-open");
                  return false;
              }
          });
      }
  });
</script>
@endpush
