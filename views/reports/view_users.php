<?php
// Function to translate role names for display
function get_thai_role_name($role_name) {
    $roles_map = [
        'Super Administrator' => 'ผู้ดูแลระบบสูงสุด',
        'Department Administrator' => 'ผู้ดูแลระบบภาควิชา',
        'Lecturer' => 'อาจารย์ผู้สอน',
        'Staff' => 'เจ้าหน้าที่',
        'Student' => 'นักศึกษา'
    ];
    return $roles_map[$role_name] ?? $role_name;
}
?>

<style>
    .report-table-card {
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(99, 102, 241, 0.05);
        border: 1px solid rgba(99, 102, 241, 0.08);
        background-color: #ffffff;
        overflow: hidden;
    }
    .report-table-header {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        padding: 24px;
        border-bottom: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .report-table-header h5 {
        color: #ffffff;
        font-weight: 700;
    }
    .report-table-header .btn-outline-light {
        border: 1px solid rgba(255, 255, 255, 0.4) !important;
        background-color: rgba(255, 255, 255, 0.1) !important;
        color: #ffffff !important;
        font-weight: 500;
        border-radius: 10px;
        padding: 8px 16px;
        transition: all 0.2s ease;
    }
    .report-table-header .btn-outline-light:hover {
        background-color: #ffffff !important;
        color: #4f46e5 !important;
        transform: translateY(-1px);
    }
    .report-table-header .btn-success {
        background-color: #10b981 !important;
        border: none !important;
        color: #ffffff !important;
        font-weight: 500;
        border-radius: 10px;
        padding: 8px 16px;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
        transition: all 0.2s ease;
    }
    .report-table-header .btn-success:hover {
        background-color: #059669 !important;
        transform: translateY(-1px);
    }
    .table-container {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #f1f5f9;
    }
    .report-data-table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.8px;
        background-color: #f8fafc !important;
        color: #64748b;
        padding: 16px 20px;
        border-bottom: 2px solid #e2e8f0;
    }
    .report-data-table td {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
        color: #334155;
    }
    .report-data-table tbody tr {
        transition: all 0.2s ease;
    }
    .report-data-table tbody tr:hover {
        background-color: rgba(99, 102, 241, 0.015) !important;
    }
    
    /* DataTable customization */
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 6px 12px;
        background-color: #f8fafc;
    }
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 4px 8px;
        background-color: #f8fafc;
    }
</style>

<div class="card report-table-card mb-4">
    <div class="report-table-header">
        <h5 class="m-0"><i class="fa-solid fa-users-viewfinder me-2"></i>วิเคราะห์พฤติกรรมและวินัยการจองของผู้ใช้งาน</h5>
        <div class="d-flex gap-2">
            <button onclick="window.print();" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-print me-1"></i> พิมพ์รายงาน</button>
            <a href="/reports/export/users" class="btn btn-success btn-sm"><i class="fa-solid fa-file-csv me-1"></i> ส่งออกไฟล์ Excel</a>
            <a href="/reports" class="btn btn-outline-light btn-sm" title="กลับหน้าหลักรายงาน"><i class="fa-solid fa-arrow-left"></i></a>
        </div>
    </div>
    <div class="card-body p-4">
        
        <div class="table-responsive table-container">
            <table class="table report-data-table align-middle m-0" id="report-table">
                <thead>
                    <tr>
                        <th>รหัสประจำตัว</th>
                        <th>ชื่อ-นามสกุลผู้ใช้งาน</th>
                        <th>ที่อยู่อีเมล</th>
                        <th>บทบาทผู้ใช้</th>
                        <th class="text-center">ยอดจองสะสม (ครั้ง)</th>
                        <th class="text-center">เข้าใช้งานสำเร็จ (ครั้ง)</th>
                        <th class="text-center">ปล่อยหมดเวลา (ครั้ง)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">ยังไม่มีการบันทึกประวัติการจองของผู้ใช้งานในระบบ</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data as $row): ?>
                            <tr>
                                <td>
                                    <?php if ($row['student_id']): ?>
                                        <span class="badge bg-secondary-subtle text-secondary fw-semibold" style="font-size: 0.8rem;">รหัสนักศึกษา: <?= esc($row['student_id']) ?></span>
                                    <?php elseif ($row['employee_id']): ?>
                                        <span class="badge bg-secondary-subtle text-secondary fw-semibold" style="font-size: 0.8rem;">รหัสพนักงาน: <?= esc($row['employee_id']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="fw-bold text-dark"><?= esc($row['first_name']) ?> <?= esc($row['last_name']) ?></span></td>
                                <td><code><?= esc($row['email']) ?></code></td>
                                <td><span class="fw-semibold text-dark"><?= esc(get_thai_role_name($row['role_name'])) ?></span></td>
                                <td class="text-center"><span class="badge bg-indigo-subtle text-indigo" style="font-size: 0.85rem; font-weight: 600;"><?= $row['total_bookings'] ?></span></td>
                                <td class="text-center"><span class="badge bg-success-subtle text-success" style="font-size: 0.85rem; font-weight: 600;"><?= $row['completed_bookings'] ?></span></td>
                                <td class="text-center"><span class="badge bg-danger-subtle text-danger" style="font-size: 0.85rem; font-weight: 600;"><?= $row['expired_bookings'] ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('#report-table').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            language: {
                search: "ค้นหาข้อมูล:",
                lengthMenu: "แสดง _MENU_ รายการต่อหน้า",
                zeroRecords: "ไม่พบข้อมูลที่ต้องการค้นหา",
                info: "แสดงรายการที่ _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                infoEmpty: "ไม่มีข้อมูลแสดง",
                infoFiltered: "(กรองจากทั้งหมด _MAX_ รายการ)",
                paginate: {
                    previous: "<i class='fa-solid fa-chevron-left'></i>",
                    next: "<i class='fa-solid fa-chevron-right'></i>"
                }
            }
        });
    });
</script>
