-- Computer Reservation Management System
-- Seed Data

-- 1. Seed Roles
INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'Super Administrator', 'Full control over the system, database, and configurations'),
(2, 'Department Administrator', 'Manages department labs, computers, and reservations'),
(3, 'Lecturer', 'Can reserve groups of computers and individual seats'),
(4, 'Staff', 'Can manage daily reservations, labs, and support users'),
(5, 'Student', 'Can reserve individual computers and track schedules');

-- 2. Seed Permissions
INSERT INTO `permissions` (`id`, `name`, `description`) VALUES
(1, 'manage_users', 'Can create, edit, delete, and view users'),
(2, 'manage_laboratories', 'Can create, edit, and delete labs'),
(3, 'manage_computers', 'Can create, edit, delete, and print QR/Barcodes for computers'),
(4, 'manage_reservations', 'Can view, approve, reject, and cancel all reservations'),
(5, 'create_reservations', 'Can request computer reservations'),
(6, 'view_reports', 'Can generate and export reports'),
(7, 'manage_settings', 'Can edit system settings and SMTP');

-- 3. Seed Role Permissions (RBAC mapping)
-- Super Administrator (all permissions)
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6), (1, 7),
-- Department Administrator (almost all permissions)
(2, 1), (2, 2), (2, 3), (2, 4), (2, 5), (2, 6), (2, 7),
-- Lecturer
(3, 5), (3, 6),
-- Staff
(4, 3), (4, 4), (4, 5), (4, 6),
-- Student
(5, 5);

-- 4. Seed Reservation Status
INSERT INTO `reservation_status` (`id`, `name`, `description`) VALUES
(1, 'Pending', 'Reservation has been requested and is awaiting review'),
(2, 'Approved', 'Reservation is approved, waiting for check-in'),
(3, 'Rejected', 'Reservation has been declined by an administrator'),
(4, 'Cancelled', 'Reservation has been cancelled by the user'),
(5, 'Completed', 'User checked in and checked out successfully'),
(6, 'Expired', 'User failed to check in within the allowed timeframe');

-- 5. Seed Core Settings
INSERT INTO `settings` (`key`, `value`) VALUES
('dept_name', 'Department of Computer Engineering'),
('academic_year', '2026-2027'),
('semester', 'Semester 1'),
('max_reservation_hours', '3'),
('max_reservations_per_user', '5'),
('check_in_expiry_minutes', '15'),
('notification_email_enabled', '0'),
('smtp_host', 'smtp.mailtrap.io'),
('smtp_port', '2525'),
('smtp_user', ''),
('smtp_pass', ''),
('smtp_encryption', 'tls'),
('smtp_from_email', 'no-reply@lab.edu'),
('smtp_from_name', 'Lab Booking System'),
('timezone', 'Asia/Bangkok');

-- 6. Seed Default Users (Password is 'admin123' for all)
-- Password bcrypt hash for 'admin123' is '$2y$12$XH2NiUYOmVfI0xQATP7c.Ow8qL8J/8dhBHTlSGA7w5.So1nf8NfrG'
INSERT INTO `users` (`first_name`, `last_name`, `email`, `phone_number`, `password_hash`, `role_id`, `status`, `employee_id`, `student_id`) VALUES
('Super', 'Admin', 'admin@lab.edu', '0123456789', '$2y$12$XH2NiUYOmVfI0xQATP7c.Ow8qL8J/8dhBHTlSGA7w5.So1nf8NfrG', 1, 'active', 'EMP001', NULL),
('Dept', 'Admin', 'dept_admin@lab.edu', '0123456788', '$2y$12$XH2NiUYOmVfI0xQATP7c.Ow8qL8J/8dhBHTlSGA7w5.So1nf8NfrG', 2, 'active', 'EMP002', NULL),
('John', 'Lecturer', 'lecturer@lab.edu', '0123456787', '$2y$12$XH2NiUYOmVfI0xQATP7c.Ow8qL8J/8dhBHTlSGA7w5.So1nf8NfrG', 3, 'active', 'EMP003', NULL),
('Jane', 'Staff', 'staff@lab.edu', '0123456786', '$2y$12$XH2NiUYOmVfI0xQATP7c.Ow8qL8J/8dhBHTlSGA7w5.So1nf8NfrG', 4, 'active', 'EMP004', NULL),
('Alice', 'Student', 'student@lab.edu', '0123456785', '$2y$12$XH2NiUYOmVfI0xQATP7c.Ow8qL8J/8dhBHTlSGA7w5.So1nf8NfrG', 5, 'active', NULL, 'STD001');

-- 7. Seed Laboratories (Sample data)
INSERT INTO `laboratories` (`code`, `name`, `building`, `floor`, `capacity`, `description`, `status`) VALUES
('LAB101', 'Software Engineering Lab', 'Engineering Building 1', '1st Floor', 30, 'Used for software design, programming, and web development courses.', 'active'),
('LAB102', 'Networking & Security Lab', 'Engineering Building 1', '1st Floor', 25, 'Equipped with Cisco routers, switches, and network analysis tools.', 'active'),
('LAB201', 'Artificial Intelligence Lab', 'IT Building', '2nd Floor', 20, 'High-end workstation lab with GPU support for machine learning.', 'active');

-- 8. Seed Computers (Sample data for LAB101)
INSERT INTO `computers` (`code`, `name`, `asset_number`, `laboratory_id`, `brand`, `model`, `cpu`, `ram`, `storage`, `operating_system`, `ip_address`, `status`) VALUES
('COMP101-01', 'SE-Workstation-01', 'ASSET-2026-001', 1, 'Dell', 'OptiPlex 7090', 'Intel Core i7-11700', '16GB DDR4', '512GB NVMe SSD', 'Windows 11 Pro', '192.168.1.10', 'available'),
('COMP101-02', 'SE-Workstation-02', 'ASSET-2026-002', 1, 'Dell', 'OptiPlex 7090', 'Intel Core i7-11700', '16GB DDR4', '512GB NVMe SSD', 'Windows 11 Pro', '192.168.1.11', 'available'),
('COMP101-03', 'SE-Workstation-03', 'ASSET-2026-003', 1, 'Dell', 'OptiPlex 7090', 'Intel Core i7-11700', '16GB DDR4', '512GB NVMe SSD', 'Windows 11 Pro', '192.168.1.12', 'maintenance'),
('COMP101-04', 'SE-Workstation-04', 'ASSET-2026-004', 1, 'Dell', 'OptiPlex 7090', 'Intel Core i7-11700', '16GB DDR4', '512GB NVMe SSD', 'Windows 11 Pro', '192.168.1.13', 'available'),
('COMP101-05', 'SE-Workstation-05', 'ASSET-2026-005', 1, 'Dell', 'OptiPlex 7090', 'Intel Core i7-11700', '16GB DDR4', '512GB NVMe SSD', 'Windows 11 Pro', '192.168.1.14', 'offline'),
('COMP102-01', 'NET-Workstation-01', 'ASSET-2026-010', 2, 'HP', 'EliteDesk 800', 'AMD Ryzen 7 5700G', '16GB DDR4', '512GB SSD', 'Ubuntu 22.04 LTS', '192.168.2.10', 'available'),
('COMP102-02', 'NET-Workstation-02', 'ASSET-2026-011', 2, 'HP', 'EliteDesk 800', 'AMD Ryzen 7 5700G', '16GB DDR4', '512GB SSD', 'Ubuntu 22.04 LTS', '192.168.2.11', 'available'),
('COMP201-01', 'AI-Workstation-01', 'ASSET-2026-020', 3, 'Lenovo', 'ThinkStation P350', 'Intel Core i9-11900K / NVIDIA RTX 3080', '32GB DDR4', '1TB NVMe SSD', 'Ubuntu 22.04 LTS', '192.168.3.10', 'available');
