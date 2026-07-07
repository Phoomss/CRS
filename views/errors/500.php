<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 Internal Server Error - LabReserve</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #cbd5e1 100%);
            color: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .error-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 24px;
            padding: 50px 40px;
            text-align: center;
            max-width: 500px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.06);
        }

        .error-code {
            font-size: 6rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ef4444 0%, #f97316 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
            line-height: 1;
        }

        .btn-indigo {
            background-color: #6366f1;
            color: #fff;
            border-radius: 12px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-indigo:hover {
            background-color: #4f46e5;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.25);
        }
    </style>
</head>
<body>
    <div class="error-card">
        <h1 class="error-code">500</h1>
        <h3 class="fw-bold text-dark mt-3 mb-2">Internal Server Error</h3>
        <p class="text-secondary small mb-4">Something went wrong on our servers. Please refresh the page, or if the problem persists, contact the department laboratory administrator.</p>
        <a href="/dashboard" class="btn btn-indigo">Return to Dashboard</a>
    </div>
</body>
</html>
