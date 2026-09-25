<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chhabra Sports — India's Premier Online Sports & Racquet Store</title>
  
  <meta name="description" content="Chhabra Sports - India's best online sports store for Tennis, Badminton, Cricket, Football, Squash & Padel racquets, shoes, strings and pro equipment since 1998. Sourced directly from SS, Yonex, Head, Babolat, Adidas, SG & Li-Ning.">
  <meta name="keywords" content="Chhabra Sports, racquets4u, badminton racquets, tennis racquets, cricket bats, Yonex Astrox, Head tennis racquet, Babolat, pro stringing India">

  <!-- Typography -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <!-- Template Icons (Font Awesome & Simple Line Icons) -->
  <link rel="stylesheet" href="/admin-assets/css/fonts.min.css">
  
  <!-- Kaiadmin Stylesheets (Preloaded on Admin routes to eliminate flash of unstyled content) -->
  @if(request()->is('admin*') || request()->path() === 'admin')
    <link rel="stylesheet" href="/admin-assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/admin-assets/css/plugins.min.css">
    <link rel="stylesheet" href="/admin-assets/css/kaiadmin.min.css">
    <link rel="stylesheet" href="/admin-assets/css/kaiadmin.css">
  @endif

  <!-- Favicon Icons -->
  <link rel="icon" type="image/png" href="/favicon.png">
  <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
  <link rel="apple-touch-icon" href="/apple-touch-icon.png">

  <!-- Initial Preloader Styles (Zero-dependency, instant visual display) -->
  <style>
    #app-initial-splash {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background: #0F172A;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      z-index: 99999999;
      font-family: 'Public Sans', sans-serif;
      transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    .splash-logo-card {
      width: 80px;
      height: 80px;
      background: #ffffff;
      border-radius: 18px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 20px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5), 0 0 20px rgba(21, 114, 232, 0.3);
      animation: splashPulse 1.8s ease-in-out infinite;
    }
    .splash-logo-card img {
      width: 52px;
      height: 52px;
      object-fit: contain;
    }
    .splash-title {
      font-size: 20px;
      font-weight: 800;
      letter-spacing: 2.5px;
      color: #FFFFFF;
      text-transform: uppercase;
      margin-bottom: 16px;
    }
    .splash-spinner {
      width: 30px;
      height: 30px;
      border: 3px solid rgba(255, 255, 255, 0.15);
      border-top-color: #1572E8;
      border-radius: 50%;
      animation: splashSpin 0.75s linear infinite;
    }
    @keyframes splashSpin {
      to { transform: rotate(360deg); }
    }
    @keyframes splashPulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.05); }
    }
  </style>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="court-pattern">
  <!-- Instant Initial Splash Loader (Only logo and Chhabra Sports) -->
  <div id="app-initial-splash">
    <div class="splash-logo-card">
      <img src="/favicon.png" alt="Chhabra Sports">
    </div>
    <div class="splash-title">CHHABRA SPORTS</div>
    <div class="splash-spinner"></div>
  </div>

  <div id="app"></div>

  <script>
    // Fade out and remove splash preloader once Vue app initializes
    window.addEventListener('load', function() {
      setTimeout(function() {
        var splash = document.getElementById('app-initial-splash');
        if (splash) {
          splash.style.opacity = '0';
          splash.style.visibility = 'hidden';
          setTimeout(function() { splash.remove(); }, 350);
        }
      }, 250);
    });
  </script>
</body>
</html>
