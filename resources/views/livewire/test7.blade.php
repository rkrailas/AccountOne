<div class="container">
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
                <div class="col-3">
                    <label>รหัสทรัพย์สิน</label>
                    <input type="text" class="form-control form-control-sm">
                </div>
                <div class="col-9">
                    <label>รายละเอียด</label>
                    <input type="text" class="form-control form-control-sm">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-3">
                    <label>ประเภท</label>
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
                    <label>สถานที่ตั้ง</label>
                    <select class="form-control form-control-sm"></select>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-3">
                    <label>ฝ่าย</label>
                    <select class="form-control form-control-sm"></select>
                </div>
                <div class="col-3">
                    <label>แผนก</label>
                    <select class="form-control form-control-sm"></select>
                </div>
                <div class="col-3">
                    <label>ผู้ดูแลทรัพย์สิน</label>
                    <select class="form-control form-control-sm"></select>
                </div>
                <div class="col-3">
                    <label>ชนิดจัดสรร</label>
                    <select class="form-control form-control-sm"></select>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">การคำนวณค่าเสื่อม</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-3">
                    <label>วิธีคำนวณ</label>
                    <select class="form-control form-control-sm"></select>
                </div>
                <div class="col-3">
                    <label>บัญชีทรัพย์สิน</label>
                    <select class="form-control form-control-sm"></select>
                </div>
                <div class="col-3">
                    <label>บัญชีค่าเสื่อมสะสม</label>
                    <select class="form-control form-control-sm"></select>
                </div>
                <div class="col-3">
                    <label>บัญชีค่าใช้จ่ายค่าเสื่อม</label>
                    <select class="form-control form-control-sm"></select>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-3">
                    <label>ราคาทุน</label>
                    <input type="number" step="0.01" class="form-control form-control-sm">
                </div>
                <div class="col-3">
                    <label>อายุการใช้งาน</label>
                    <input type="number" step="1" class="form-control form-control-sm">
                </div>
                <div class="col-3">
                    <label>อัตรา (%)</label>
                    <input type="number" step="0.01" class="form-control form-control-sm">
                </div>
                <div class="col-3">
                    <label>ค่าเสื่อมสะสม</label>
                    <input type="number" step="0.01" class="form-control form-control-sm" readonly>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-3">
                    <label>วันที่ซื้อ</label>
                    <select class="form-control form-control-sm"></select>
                </div>
                <div class="col-3">
                    <label>วันที่เริ่มคิดค่าเสื่อม</label>
                    <select class="form-control form-control-sm"></select>
                </div>
                <div class="col-3">
                    <label>มูลค่าตามบัญชี</label>
                    <input type="number" step="0.01" class="form-control form-control-sm">
                </div>
                <div class="col-3">
                    <label>มูลค่าซาก</label>
                    <input type="number" step="0.01" class="form-control form-control-sm">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-3">
                    <label>คำนวณค่าเสื่อมจนถึง</label>
                    <select class="form-control form-control-sm"></select>
                </div>
                <div class="col-3">
                    <label>บันทึกค่าเสื่อมล่าสุด</label>
                    <select class="form-control form-control-sm"></select>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">รายละเอียดื่อน ๆ</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-6">
                    <label>ผู้ขาย</label>
                    <select class="form-control form-control-sm"></select>
                </div>
                <div class="col-6">
                    <label>เลขที่เอกสาร</label>
                    <input type="text" class="form-control form-control-sm">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-3">
                    <label>หมายเลขเครื่อง</label>
                    <input type="text" class="form-control form-control-sm">
                </div>
                <div class="col-3">
                    <label>เอกสารอ้างอิง-1</label>
                    <input type="text" class="form-control form-control-sm">
                </div>
                <div class="col-3">
                    <label>เอกสารอ้างอิง-2</label>
                    <input type="text" class="form-control form-control-sm">
                </div>
                <div class="col-3">
                    <label>สิ้นสุดการรับประกัน</label>
                    <select class="form-control form-control-sm"></select>
                </div>
            </div>
        </div>
    </div>
</div>
