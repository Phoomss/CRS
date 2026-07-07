<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Computer Reservation System - Login</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --bg-gradient: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 50%, #cbd5e1 100%);
            --glass-bg: rgba(255, 255, 255, 0.75);
            --glass-border: rgba(0, 0, 0, 0.08);
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --accent-color: #6366f1;
            --accent-hover: #4f46e5;

            /* Native Bootstrap 5 Overrides */
            --bs-primary: var(--accent-color) !important;
            --bs-primary-rgb: 99, 102, 241 !important;
            --bs-link-color: var(--accent-color) !important;
            --bs-link-hover-color: var(--accent-hover) !important;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg-gradient);
            background-attachment: fixed;
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            margin: 0;
            position: relative;
        }

        /* Ambient Glow Blobs */
        body::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.08) 0%, rgba(0,0,0,0) 70%);
            top: 10%;
            left: 10%;
            z-index: -1;
        }
        body::after {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(168, 85, 247, 0.06) 0%, rgba(0,0,0,0) 70%);
            bottom: 10%;
            right: 10%;
            z-index: -1;
        }

        .auth-container {
            width: 100%;
            max-width: 430px;
            padding: 15px;
            animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .auth-card {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 40px 35px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.06);
        }

        .auth-logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .auth-logo i {
            font-size: 3rem;
            color: var(--accent-color);
            margin-bottom: 15px;
            filter: drop-shadow(0 0 10px rgba(99, 102, 241, 0.25));
            animation: pulse 2s infinite ease-in-out;
        }

        .auth-logo h2 {
            font-weight: 700;
            letter-spacing: -0.5px;
            font-size: 1.6rem;
            margin: 0;
            color: #0f172a;
        }

        .auth-logo p {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .form-label {
            font-weight: 500;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .input-group {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(0, 0, 0, 0.12);
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .input-group:focus-within {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        .input-group-text {
            background: transparent;
            border: none;
            color: var(--text-secondary);
            padding-left: 15px;
        }

        .form-control {
            background: transparent;
            border: none;
            color: var(--text-primary);
            padding: 12px 15px;
            font-size: 0.95rem;
        }

        .form-control:focus {
            background: transparent;
            border: none;
            color: var(--text-primary);
            box-shadow: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-color) 0%, #4f46e5 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
            background: linear-gradient(135deg, var(--accent-hover) 0%, #4338ca 100%);
            color: #fff;
        }

        .auth-footer {
            text-align: center;
            margin-top: 25px;
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .auth-footer code {
            color: #4f46e5;
            background: rgba(99, 102, 241, 0.06);
            padding: 2px 6px;
            border-radius: 4px;
        }

        .auth-footer a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .auth-footer a:hover {
            color: var(--text-primary);
            text-decoration: underline;
        }

        .btn-close-white {
            filter: none !important;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }
    </style>
</head>
<body>

    <div class="auth-container">
        <!-- Render views dynamically -->
        <?= $content ?>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
