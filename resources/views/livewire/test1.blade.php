<div class="container">
    <div class="row">
        <div class="col-3">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-info"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">จำนวนคงเหลือ</span>
                    <span class="info-box-number">1,410</span>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-dollar-sign"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">มูลค่าคงเหลือ</span>
                    <span class="info-box-number">2000,000.00</span>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">ซื้อครั้งล่าสุด</span>
                    <span class="info-box-number">1 กันยายน 2564</span>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="far fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">ขายครั้งล่าสุด</span>
                    <span class="info-box-number">5 กันยายน 2564</span>
                </div>
            </div>
        </div>
    </div>
    <div class="card card-primary mt-2">
        <div class="card-header">
            <h3 class="card-title">ข้อมูลทั่วไป</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-2">
                    <label>รหัสสินค้า</label>
                    <input type="text" class="form-control form-control-sm">
                </div>
                <div class="col-7">
                    <label>รายละเอียดสินค้า</label>
                    <input type="text" class="form-control form-control-sm">
                </div>
                <div class="col-3">
                    <label>ชนิดสินค้า</label>
                    <select class="form-control form-control-sm"></select>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-3">
                    <label>ประเภทสินค้า</label>
                    <select class="form-control form-control-sm"></select>
                </div>
                <div class="col-3">
                    <label>กลุ่มสินค้า-1</label>
                    <select class="form-control form-control-sm"></select>
                </div>
                <div class="col-3">
                    <label>กลุ่มสินค้า-2</label>
                    <select class="form-control form-control-sm"></select>
                </div>
                <div class="col-3">
                    <label>สถานที่เก็บ</label>
                    <select class="form-control form-control-sm"></select>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-3">
                    <label>หน่วยซื้อ</label>
                    <select class="form-control form-control-sm"></select>
                </div>
                <div class="col-3">
                    <label>หน่วยขาย</label>
                    <select class="form-control form-control-sm"></select>
                </div>
                <div class="col-3">
                    <label>ทุนต่อหน่วย</label>
                    <input type="text" class="form-control form-control-sm" readonly>
                </div>
                <div class="col-3">
                    <label>ราคาขาย</label>
                    <input type="number" step="0.01" class="form-control form-control-sm">
                </div>
            </div>
        </div>
    </div>
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">ข้อมูลบัญชี</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-3">
                    <label>บันทึกบัญชี</label>
                    <select class="form-control form-control-sm"></select>
                </div>
                <div class="col-3">
                    <label>บัญชีขาย</label>
                    <select class="form-control form-control-sm"></select>
                </div>
                <div class="col-3">
                    <label>บัญชีส่งคืน-ซื้อ</label>
                    <select class="form-control form-control-sm"></select>
                </div>
                <div class="col-3">
                    <label>บัญชีส่งคืน-ขาย</label>
                    <select class="form-control form-control-sm"></select>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-3">
                    <label>วิธีคำนวณต้นทุน</label>
                    <select class="form-control form-control-sm"></select>
                </div>
                <div class="col-3">
                    <label>ต้นทุนมาตราฐาน</label>
                    <input type="number" step="0.01" class="form-control form-control-sm">
                </div>
            </div>
        </div>
    </div>

    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">อื่นๆ</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-2">

                <div class="col-3">
                    <label>จำนวนขั้นต้ำ</label>
                    <input type="text" class="form-control form-control-sm">
                </div>
                <div class="col-3">
                    <label>จำนวนสั่ง</label>
                    <input type="text" class="form-control form-control-sm">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-12">
                    <label>บันทึก</label>
                    <textarea class="form-control form-control-sm" rows="2"></textarea>
                </div>

            </div>
        </div>
    </div>

</div>

@include('livewire.accstar._mycss')