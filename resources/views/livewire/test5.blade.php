<div class="container">
  <div class="card card-primary mt-2">
    <div class="card-body">
      <div class="row mb-2">
        <div class="col-4">
          <label>รหัสสินค้า</label>
          <input type="text" class="form-control form-control-sm">
        </div>
        <div class="col-8">
          <label>รายละเอียดสินค้า</label>
          <input type="text" class="form-control form-control-sm">
        </div>
      </div>
      <div class="row mb-2">
        <div class="col-4">
          <label>ชนิดสินค้า</label>
          <select class="form-control form-control-sm"></select>
        </div>
        <div class="col-4">
          <label>ประเภทสินค้า</label>
          <select class="form-control form-control-sm"></select>
        </div>
        <div class="col-4">
          <label>สถานที่เก็บ</label>
          <select class="form-control form-control-sm"></select>
        </div>
      </div>
    </div>
  </div>

  <div class="card card-primary mt-2">
    <div class="card-body">
      <div class="row mb-2">
        <div class="col-4">
          <label class="">วันที่ปรับปรุง</label>
          <div class="input-group mb-1">
            <div class="input-group-prepend">
              <span class="input-group-text">
                <i class="fas fa-calendar"></i>
              </span>
            </div>
            <x-datepicker wire:model.defer="soHeader.sodate" id="soDate" :error="'date'" required />
          </div>
        </div>
        <div class="col-4">
          <label>หมายเลขอ้างอิง</label>
          <input type="text" class="form-control form-control-sm">
        </div>
        <div class="col-4">
          <label>ประเภทปรับปรุง</label>
          <div class="form-check">
            <input class="form-check-input" type="radio" value="option1" checked>
            <label class="form-check-label">
              ปรับปรุง-เข้า
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" value="option2">
            <label class="form-check-label">
              ปรับปรุง-ออก
            </label>
          </div>
        </div>
      </div>

      <div class="row mb-2">
        <div class="col-4">
          <label>จำนวน</label>
          <div class="row">
            <div class="col-8"> 
              <input type="number" step="0.01" class="form-control form-control-sm" required style="text-align: right;">
            </div>
            <div class="col-4">
              <input type="text" class="form-control form-control-sm" readonly>
            </div>
          </div>
        </div>
        <div class="col-4">
          <label>ต้นทุน/หน่วย</label>
          <input type="number" step="0.01" class="form-control form-control-sm" required style="text-align: right;">
        </div>
        <div class="col-4">
          <label>บัญชีเจ้าหนี้/ลูกหนี้</label>
          <select class="form-control form-control-sm"></select>
        </div>
      </div>

      <div class="row mb-2">
        <div class="col-4">
          <label>ทุนรวม</label>
            <input type="number" step="0.01" class="form-control form-control-sm" required style="text-align: right;" readonly>
        </div>
        <div class="col-4">
          <label>คงเหลือ</label>
          <input type="number" step="0.01" class="form-control form-control-sm" required style="text-align: right;" readonly>
        </div>
        <div class="col-4">
          <label>มูลค่าคงเหลือ</label>
          <input type="number" step="0.01" class="form-control form-control-sm" required style="text-align: right;" readonly>
        </div>
      </div>
    </div>
  </div>
</div>