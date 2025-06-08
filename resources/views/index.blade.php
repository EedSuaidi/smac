<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>
    <link rel="shortcut icon" href="{{ asset('images/SMAC-noBG.png') }}" type="image/x-icon">
    <style>
        html,
        body {}

        body {
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f8f9fa;
            color: #222;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 48px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo {
            font-size: 2rem;
            font-weight: bold;
            color: #00bc7d;
            letter-spacing: 1px;
            text-shadow: none;
        }

        .logo-img {
            width: 100px;
            height: 50px;
            object-fit: contain;
            display: block;
        }

        .nav-buttons {
            display: flex;
            gap: 12px;
        }

        .nav-buttons button {
            padding: 8px 20px;
            border: 1.5px solid #fff;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
            color: #000;
            background: transparent;
        }

        .login-btn a {
            background: #fff;
            color: #00bc7d;
            border: 1.5px solid #00bc7d;
            border-radius: 4px;
            padding: 8px 20px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
            text-decoration: none;
        }

        .login-btn:hover a {
            background: rgb(0, 0, 0);
            color: #fff
        }

        .register-btn a {
            background: #fff;
            color: #00bc7d;
            border: 1.5px solid #00bc7d;
            border-radius: 4px;
            padding: 8px 20px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
            text-decoration: none;
        }

        .register-btn:hover a {
            background: rgb(24, 24, 24);
            color: #fff;
        }

        .hero {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            text-align: left;
            padding: 32px 24px;
            height: 60vh;
            min-height: 0;
            gap: 48px;
            box-sizing: border-box;
        }

        .hero-left {
            flex: 1;
            min-height: 600px;
            background-image: url('/images/crypto.png');
            background-size: contain;
            background-position: center;
            background-repeat: no-repeat;
            margin: 0;
            position: relative;
            overflow: hidden;
            border-radius: 12px;
        }

        .hero-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            text-align: left;
            padding-left: 32px;
        }

        .hero-image {
            width: 350px;
            max-width: 90vw;
            margin-bottom: 32px;
            border-radius: 4px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
        }

        .tagline {
            color: #222;
            font-size: 2.6rem;
            font-weight: 700;
            margin-bottom: 16px;
            background: none;
            padding: 0;
            border-radius: 0;
            text-shadow: none;
            display: block;
        }

        .subtitle {
            color: #555;
            font-size: 1.3rem;
            background: none;
            padding: 0;
            border-radius: 0;
            text-shadow: none;
        }

        .search-bar {
            margin: 32px 0 0 0;
            display: flex;
            gap: 8px;
            width: 100%;
            max-width: 400px;
        }

        .search-bar input {
            flex: 1;
            padding: 10px 16px;
            border: 1.5px solid #00bc7d;
            border-radius: 4px 0 0 4px;
            font-size: 1rem;
            outline: none;
        }

        .search-bar button {
            padding: 10px 20px;
            background: #00bc7d;
            color: #fff;
            border: none;
            border-radius: 0 4px 4px 0;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s;
        }

        .features {
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            gap: 24px;
            background: #fff;
            padding: 32px 0 0 0;
            margin-top: 16px;
            margin-bottom: 0;
            flex: 1 0 auto;
        }

        .feature-card {
            background: #f8f9fa;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
            padding: 16px 8px;
            max-width: 220px;
            text-align: center;
            transition: box-shadow 0.2s;
        }

        .feature-card:hover {
            box-shadow: 0 6px 24px rgba(32, 190, 255, 0.12);
        }

        .feature-card h3 {
            color: #00bc7d;
            margin-bottom: 12px;
        }

        .feature-card p {
            color: #555;
            font-size: 1.05rem;
        }

        .footer {
            background: #fff;
            color: #888;
            text-align: center;
            padding: 8px 0 8px 0;
            margin-top: auto;
            font-size: 0.9rem;
            border-top: 1px solid #eee;
        }

        .cta-btn {
            margin: 24px 0 16px 0;
            padding: 14px 36px;
            background: #00bc7d;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .cta-btn:hover {
            background: #028d5f;
        }

        .stats {
            display: flex;
            gap: 32px;
            margin-top: 12px;
            justify-content: flex-start;
        }

        .stats div {
            text-align: center;
        }

        .stat-number {
            display: block;
            font-size: 1.4rem;
            font-weight: bold;
            color: #00bc7d;
        }

        .stat-label {
            display: block;
            font-size: 0.95rem;
            color: #555;
        }

        @media (max-width: 800px) {
            .stats {
                justify-content: center;
                gap: 18px;
            }

            .navbar {
                flex-direction: column;
                align-items: flex-start;
                padding: 12px 10px;
            }

            .logo-img {
                width: 80px;
                height: 40px;
            }

            .nav-buttons {
                margin-top: 8px;
                width: 100%;
                justify-content: flex-start;
            }
        }

        @media (max-width: 600px) {
            .navbar {
                flex-direction: column;
                align-items: flex-start;
                padding: 16px 12px;
            }

            .hero-image {
                width: 100%;
            }

            .tagline {
                font-size: 1.3rem;
            }
        }

        @media (max-width: 800px) {
            .navbar {
                padding: 12px 10px;
            }

            .hero {
                padding: 40px 8px 32px 8px;
            }

            .tagline {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 900px) {
            .hero {
                flex-direction: column;
                padding: 24px 8px 16px 8px;
                height: auto;
                min-height: 0;
                gap: 24px;
                text-align: center;
            }

            .hero-left,
            .hero-right {
                width: 100%;
                min-height: 0;
                border-radius: 8px;
                padding: 0;
                align-items: center;
                text-align: center;
            }

            .hero-left {
                background-image: none;
            }

            .hero-image {
                width: 100%;
                max-width: 90%;
                margin-bottom: 16px;
            }

            .hero-right {
                padding-left: 0;
                align-items: center;
            }

            .features {
                flex-direction: column;
                gap: 12px;
                padding: 12px 0 0 0;
            }

            .feature-card {
                width: 90%;
                max-width: 350px;
                margin: 0 auto;
            }
        }

        @media (min-width: 901px) {
            .hero-image {
                display: none;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <img src="{{ asset('images/SMAC-noBG.png') }}" alt="SMAC Logo" class="logo-img">
        </div>
        <div class="nav-buttons">
            <button class="login-btn"><a href="/dashboard/login">Login</a></button>
            <button class="register-btn"><a href="/dashboard/register">Register</a></button>
        </div>
    </nav>
    <section class="hero">
        <div class="hero-left">
        </div>
        <div class="hero-right">
            <img src="{{ asset('images/crypto.png') }}" alt="" class="hero-image">
            <div class="tagline">Selamat Datang di SMAC</div>
            <div class="subtitle">Solusi terbaik dari yang terbaik tanpa menambahkan solusi <br> solusi baru yang baru
                baru ini</div>

            <button class="cta-btn">Mulai Sekarang</button>

            <div class="stats">
                <div>
                    <span class="stat-number">10K+</span>
                    <span class="stat-label">Pengguna</span>
                </div>
                <div>
                    <span class="stat-number">99.9%</span>
                    <span class="stat-label">Keamanan</span>
                </div>
                <div>
                    <span class="stat-number">24/7</span>
                    <span class="stat-label">Support</span>
                </div>
            </div>
        </div>
    </section>
    <section class="features">
        <div class="feature-card">
            <h3>Transaksi Aman</h3>
            <p>Keamanan data dan transaksi terjamin dengan teknologi enkripsi terbaru.</p>
        </div>
        <div class="feature-card">
            <h3>Analisis Cerdas</h3>
            <p>Dapatkan insight keuangan otomatis dan rekomendasi terbaik untuk Anda.</p>
        </div>
        <div class="feature-card">
            <h3>Dukungan 24/7</h3>
            <p>Tim support kami siap membantu kapan saja Anda butuhkan.</p>
        </div>
    </section>
    <footer class="footer">
        &copy; 2025 SMAC. All rights reserved.
    </footer>
</body>

</html>
