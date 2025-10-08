<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">  
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Art Gallery - Home</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .gallery-section {
            padding: 80px 0;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }
        
        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 15px;
            position: relative;
            display: inline-block;
        }
        
        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            border-radius: 2px;
        }
        
        .section-title p {
            color: #7f8c8d;
            font-size: 1.1rem;
            margin-top: 20px;
        }
        
        .art-card {
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.4s ease;
            background: white;
            margin-bottom: 30px;
            height: 400px;
        }
        
        .art-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        
        .art-image-container {
            position: relative;
            width: 100%;
            height: 280px;
            overflow: hidden;
        }
        
        .art-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .art-card:hover .art-image {
            transform: scale(1.1);
        }
        
        .art-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(180deg, transparent 0%, rgba(0,0,0,0.7) 100%);
            opacity: 0;
            transition: opacity 0.4s ease;
            display: flex;
            align-items: flex-end;
            padding: 20px;
        }
        
        .art-card:hover .art-overlay {
            opacity: 1;
        }
        
        .overlay-content {
            color: white;
            width: 100%;
        }
        
        .overlay-content .btn {
            background: white;
            color: #667eea;
            border: none;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .overlay-content .btn:hover {
            background: #667eea;
            color: white;
        }
        
        .art-info {
            padding: 20px;
        }
        
        .art-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 8px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .art-artist {
            color: #7f8c8d;
            font-size: 0.95rem;
            margin-bottom: 10px;
        }
        
        .art-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: #667eea;
        }
        
        .art-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            z-index: 10;
        }
        
        .view-all-btn {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 30px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .view-all-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
            color: white;
        }
        
        .stats-section {
            background: white;
            padding: 60px 0;
            text-align: center;
        }
        
        .stat-item {
            padding: 20px;
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .stat-label {
            color: #7f8c8d;
            font-size: 1.1rem;
            margin-top: 10px;
        }
    </style>
</head>
<body class="home-page">
   <nav class="d-flex align-items-center fixed-top shadow-sm" 
     style="background: white; width: 100%; padding: 10px 40px; z-index: 1000;">
    <div class="logo">
        <img src="image/logo.jpg" alt="Art Gallery Logo" style="height: 50px;">
    </div>
    <div class="d-flex align-items-center flex-grow-1 justify-content-end" style="gap: 32px;">
       <?php 
       $isLoggedIn = (
           isset($_SESSION['user_id']) || 
           isset($_SESSION['username']) || 
           isset($_SESSION['user_email']) ||
           isset($_SESSION['email']) ||
           isset($_SESSION['loggedin']) ||
           isset($_SESSION['user'])
       );
       $isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true; 
       
       if($isLoggedIn): 
           $displayName = '';
           if(isset($_SESSION['username'])) {
               $displayName = $_SESSION['username'];
           } elseif(isset($_SESSION['user_email'])) {
               $displayName = $_SESSION['user_email'];
           } elseif(isset($_SESSION['email'])) {
               $displayName = $_SESSION['email'];
           } elseif(isset($_SESSION['name'])) {
               $displayName = $_SESSION['name'];
           } else {
               $displayName = 'User';
           }
       ?>
           <span class="navbar-text text-dark fw-bold me-2">
               Welcome, <?php echo htmlspecialchars($displayName); ?>!
           </span>
           <a href="logout.php" class="btn btn-nav ">Logout</a>
           <a href="user_dashboard.php" class="btn btn-nav">Dashboard</a>
        

       <?php else: ?>
           <a href="login.php" class="btn btn-nav">Login</a>
           <a href="registration.php" class="btn btn-nav">Register</a>
       <?php endif; ?>
       
       <a href="view_orders.php" class="btn btn-nav">View Artworks</a>
       <a href="contact.php" class="btn btn-nav">Contact</a>
       <div class="dropdown">
           <a class="btn btn-nav dropdown-toggle" href="#" id="aboutDropdown" data-bs-toggle="dropdown" aria-expanded="false">About</a>
           <ul class="dropdown-menu" aria-labelledby="aboutDropdown">
               <li><h6 class="dropdown-header">Art Details</h6></li>
               <li><a class="dropdown-item" href="about.php#modern">Modern Art</a></li>
               <li><a class="dropdown-item" href="about.php#classic">Classic Art</a></li>
               <li><a class="dropdown-item" href="about.php#featured">Featured Artworks</a></li>
               <li><hr class="dropdown-divider"></li>
               <li><a class="dropdown-item" href="view_orders.php">View All Artworks</a></li>
           </ul>
       </div>         
    </div>
</nav>

 <div class="hero-banner">
        <div class="container">
            <div class="row align-items-center">
        <div class="col-12 text-center hero-text">
          <h1 class="vibeart-heading mb-3">Discover a new kind of art.</h1>
          <h2 class="vibeart-subheading">
            Discover, Admire, and Own Exquisite Artwork from Talented Artists Worldwide
          </h2>
        </div>
            </div>
        </div>
   </div>
   <section class="stats-section">
       <div class="container">
           <div class="row">
               <div class="col-md-3 col-6">
                   <div class="stat-item">
                       <div class="stat-number">500+</div>
                       <div class="stat-label">Artworks</div>
                   </div>
               </div>
               <div class="col-md-3 col-6">
                   <div class="stat-item">
                       <div class="stat-number">200+</div>
                       <div class="stat-label">Artists</div>
                   </div>
               </div>
               <div class="col-md-3 col-6">
                   <div class="stat-item">
                       <div class="stat-number">1000+</div>
                       <div class="stat-label">Happy Customers</div>
                   </div>
               </div>
           </div>
       </div>
   </section>

<footer class="bg-light mt-0 py-4">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-8 text-md-start text-center mb-2 mb-md-0">
        <div class="footer-links">
          <a href="index.php">Home</a>
          <a href="about.php">About</a>
          <a href="contact.php">Contact</a>
          <a href="privacy.php">Privacy Policy</a>
          <a href="terms.php">Terms & Conditions</a>
        </div>
      </div>
      <div class="col-md-4 text-md-end text-center">
        <a href="https://www.instagram.com/art_by_drashti__" target="_blank" title="Instagram">
          <img src="https://cdn-icons-png.flaticon.com/512/2111/2111463.png" alt="Instagram" style="height:32px; width:32px; margin-right:12px;">
        </a>
        <a href="https://www.youtube.com/channel/UChsL00K10ec3cktW9NSddXA" target="_blank" title="YouTube">
          <img src="https://cdn-icons-png.flaticon.com/512/1384/1384060.png" alt="YouTube" style="height:32px; width:32px;">
        </a>
      </div>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>