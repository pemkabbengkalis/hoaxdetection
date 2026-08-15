<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&display=swap');
        
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .container {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 3rem;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            max-width: 600px;
            width: 90%;
            transform: translateY(0);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        .error-code {
            font-size: 8rem;
            font-weight: 900;
            margin: 0;
            line-height: 1;
            background: linear-gradient(to right, #3b82f6, #0ea5e9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0px 10px 30px rgba(59, 130, 246, 0.3);
        }

        .monkey-img {
            width: 250px;
            height: 250px;
            object-fit: cover;
            border-radius: 50%;
            margin: 20px auto;
            border: 4px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 30px rgba(59, 130, 246, 0.2);
            transition: transform 0.3s ease;
        }

        .monkey-img:hover {
            transform: scale(1.05) rotate(-5deg);
        }

        .message {
            font-size: 1.75rem;
            font-weight: 700;
            margin-top: 1rem;
            margin-bottom: 2rem;
            color: #e2e8f0;
        }

        .btn {
            display: inline-block;
            padding: 12px 32px;
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px -10px rgba(37, 99, 235, 0.5);
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px -10px rgba(37, 99, 235, 0.7);
        }

        /* Decorative blobs */
        .blob-1, .blob-2 {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 1;
            opacity: 0.5;
        }

        .blob-1 {
            top: -100px;
            left: -100px;
            width: 300px;
            height: 300px;
            background: #3b82f6;
        }

        .blob-2 {
            bottom: -100px;
            right: -100px;
            width: 400px;
            height: 400px;
            background: #8b5cf6;
        }
    </style>
</head>
<body>
    <div class="blob-1"></div>
    <div class="blob-2"></div>
    
    <div class="container">
        <h1 class="error-code">404</h1>
        <img src="{{ asset('img/monkey.jpg') }}" alt="Monyet Judging" class="monkey-img">
        <p class="message">"woy monyet kurang kerjaan ya kalian"</p>
        <a href="{{ url('/') }}" class="btn">Kembali ke Habitat</a>
    </div>
</body>
</html>
