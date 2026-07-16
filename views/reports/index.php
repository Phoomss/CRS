<style>
    .report-main-card {
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(99, 102, 241, 0.05);
        border: 1px solid rgba(99, 102, 241, 0.08);
        background-color: #ffffff;
        overflow: hidden;
    }
    .report-main-header {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        padding: 24px;
        border-bottom: none;
    }
    .report-main-header h5 {
        color: #ffffff;
        font-weight: 700;
    }
    .report-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background-color: #ffffff;
        padding: 24px;
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: all 0.3s ease;
    }
    .report-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(99, 102, 241, 0.08);
        border-color: rgba(99, 102, 241, 0.25);
    }
    .icon-box {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        background-color: rgba(99, 102, 241, 0.08);
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }
    .report-card:hover .icon-box {
        background-color: #4f46e5;
        color: #ffffff;
        transform: scale(1.05);
    }
    .report-card h5 {
        font-size: 1.15rem;
        font-weight: 700;
        color: #1e293b;
    }
    .report-card p {
        color: #64748b;
        font-size: 0.88rem;
        line-height: 1.5;
    }
    .btn-view-report {
        background-color: #6366f1;
        color: #ffffff;
        font-weight: 600;
        border: none;
        border-radius: 10px;
        padding: 8px 16px;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }
    .btn-view-report:hover {
        background-color: #4f46e5;
        color: #ffffff;
    }
    .btn-export-report {
        background-color: rgba(16, 185, 129, 0.08);
        color: #059669;
        border: none;
        border-radius: 10px;
        width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .btn-export-report:hover {
        background-color: #059669;
        color: #ffffff;
    }
</style>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card report-main-card mb-4">
            <div class="report-main-header">
                <h5 class="m-0"><i class="fa-solid fa-file-invoice-dollar me-2"></i>รายงานวิเคราะห์ยอดการใช้งานและการจองเครื่อง</h5>
            </div>
            
            <div class="card-body p-4">
                <p class="text-secondary mb-4" style="font-size: 0.95rem;">กรุณาเลือกโมดูลรายงานด้านล่างเพื่อตรวจสอบสถิติเชิงลึก สั่งพิมพ์รายงาน หรือส่งออกข้อมูลสถิติเป็นไฟล์ตารางสำหรับโปรแกรม Excel</p>
                
                <div class="row g-4 mt-1">
                    <!-- Report Card 1: Computers -->
                    <div class="col-md-4">
                        <div class="report-card">
                            <div class="icon-box"><i class="fa-solid fa-display fa-2x"></i></div>
                            <h5 class="mb-2">สถิติการใช้งานเครื่องคอมพิวเตอร์</h5>
                            <p class="mb-4">ติดตามปริมาณการจองทั้งหมด, จำนวนเซสชันการเข้าใช้ที่เสร็จสมบูรณ์, จำนวนครั้งที่หมดเวลาเช็คอิน และระยะเวลาเปิดใช้งานสะสมของคอมพิวเตอร์รายเครื่อง</p>
                            <div class="d-flex gap-2 mt-auto">
                                <a href="/reports/view/computers" class="btn-view-report flex-grow-1"><i class="fa-solid fa-eye me-1"></i> ดูรายละเอียด</a>
                                <a href="/reports/export/computers" class="btn-export-report" title="ดาวน์โหลดไฟล์ Excel"><i class="fa-solid fa-download"></i></a>
                            </div>
                        </div>
                    </div>
 
                    <!-- Report Card 2: Laboratories -->
                    <div class="col-md-4">
                        <div class="report-card">
                            <div class="icon-box"><i class="fa-solid fa-door-open fa-2x"></i></div>
                            <h5 class="mb-2">สถิติความหนาแน่นห้องปฏิบัติการ</h5>
                            <p class="mb-4">วิเคราะห์ความหนาแน่นยอดจองแยกตามแต่ละห้องปฏิบัติการคอมพิวเตอร์ แสดงข้อมูลจำแนกตามการอนุมัติ การเข้าใช้ การปฏิเสธ และการยกเลิกคำขอจอง</p>
                            <div class="d-flex gap-2 mt-auto">
                                <a href="/reports/view/laboratories" class="btn-view-report flex-grow-1"><i class="fa-solid fa-eye me-1"></i> ดูรายละเอียด</a>
                                <a href="/reports/export/laboratories" class="btn-export-report" title="ดาวน์โหลดไฟล์ Excel"><i class="fa-solid fa-download"></i></a>
                            </div>
                        </div>
                    </div>
 
                    <!-- Report Card 3: Users -->
                    <div class="col-md-4">
                        <div class="report-card">
                            <div class="icon-box"><i class="fa-solid fa-users-viewfinder fa-2x"></i></div>
                            <h5 class="mb-2">รายงานพฤติกรรมการจองของผู้ใช้</h5>
                            <p class="mb-4">แสดงข้อมูลวิเคราะห์ยอดสะสมการจองที่จำแนกตามรายบุคคลและกลุ่มบทบาท พร้อมสถิติชี้วัดด้านวินัยการเข้าใช้งานและการทำตามกติกาของระบบ</p>
                            <div class="d-flex gap-2 mt-auto">
                                <a href="/reports/view/users" class="btn-view-report flex-grow-1"><i class="fa-solid fa-eye me-1"></i> ดูรายละเอียด</a>
                                <a href="/reports/export/users" class="btn-export-report" title="ดาวน์โหลดไฟล์ Excel"><i class="fa-solid fa-download"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
 
            </div>
        </div>
    </div>
</div>
