<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>View Artworks - Online Art Gallery</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="style.css">
</head>
<body class="view-artwork-page">

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
           <a href="logout.php" class="btn btn-nav">Logout</a>
           <a href="user_dashboard.php" class="btn btn-nav">Dashboard</a>
       <?php else: ?>
           <a href="login.php" class="btn btn-nav">Login</a>
           <a href="registration.php" class="btn btn-nav">Register</a>
       <?php endif; ?>
       
       <a href="home.php" class="btn btn-nav">Home</a>
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

<div style="margin-top: 100px;">
	
<div class="artwork-row">
<div class="artwork-card">
    <div class="artwork-image">
        <img src="image/tiger painting.jpg" alt="Tiger Painting">
        <div class="overlay">
            <a class="wishlist-btn" href="wishlist.php?title=Tiger%20Painting&price=500&image=image/tiger%20painting.jpg" style="text-decoration:none;">♡</a>
            <button class="cart-btn">BUY NOW</button>
        </div>
    </div>
    <div class="artwork-info">
        <h3>Tiger Painting</h3>
        <p class="price">₹500</p>
    </div>
</div>

<div class="artwork-card">
    <div class="artwork-image">
        <img src="image/colourfull.jpg" alt="Mandala Art">
        <div class="overlay">
            <a class="wishlist-btn" href="wishlist.php?title=Mandala%20Art&price=2000&image=image/colourfull.jpg" style="text-decoration:none;">♡</a>
            <button class="cart-btn">BUY NOW</button>
        </div>
    </div>
    <div class="artwork-info">
        <h3>Mandala Art</h3>
        <p class="price">₹2000</p>
    </div>
</div>

<div class="artwork-card">
    <div class="artwork-image">
        <img src="image/owl.jpg" alt="Owl Art">
        <div class="overlay">
            <a class="wishlist-btn" href="wishlist.php?title=Owl%20Art&price=800&image=image/owl.jpg" style="text-decoration:none;">♡</a>
            <button class="cart-btn">BUY NOW</button>
        </div>
    </div>
    <div class="artwork-info">
        <h3>Owl Art</h3>
        <p class="price">₹800</p>
    </div>
</div>

<div class="artwork-card">
    <div class="artwork-image">
        <img src="image/butterfly.jpg" alt="Butterfly Art">
        <div class="overlay">
            <a class="wishlist-btn" href="wishlist.php?title=Butterfly%20Art&price=1000&image=image/butterfly.jpg" style="text-decoration:none;">♡</a>
            <button class="cart-btn">BUY NOW</button>
        </div>
    </div>
    <div class="artwork-info">
        <h3>Butterfly Art</h3>
        <p class="price">₹1000</p>
    </div>
</div>    

<div class="artwork-card">
    <div class="artwork-image">
        <img src="image/krishna.jpg" alt="Krishna Art">
        <div class="overlay">
            <a class="wishlist-btn" href="wishlist.php?title=Krishna%20Art&price=500&image=image/krishna.jpg" style="text-decoration:none;">♡</a>
            <button class="cart-btn">BUY NOW</button>
        </div>
    </div>
    <div class="artwork-info">
        <h3>Krishna Art</h3>
        <p class="price">₹500</p>
    </div>
</div>    

<div class="artwork-card">
    <div class="artwork-image">
    <img src="image/lippan.jpg" alt="Lippan Art">
        <div class="overlay">
            <a class="wishlist-btn" href="wishlist.php?title=Lippan%20Art&price=1000&image=image/Lippan.jpg" style="text-decoration:none;">♡</a>
            <button class="cart-btn">BUY NOW</button>
        </div>
    </div>
    <div class="artwork-info">
        <h3>Lippan Art</h3>
        <p class="price">₹1000</p>
    </div>
</div>
        
<div class="artwork-card">
    <div class="artwork-image">
    <img src="image/mirror lippan.jpg" alt="Mirror Lippan Art">
        <div class="overlay">
            <a class="wishlist-btn" href="wishlist.php?title=Mirror Lippan%20Art&price=5000&image=image/Mirror Lippan.jpg" style="text-decoration:none;">♡</a>
            <button class="cart-btn">BUY NOW</button>
        </div>
    </div>
    <div class="artwork-info">
        <h3>Mirror Lippan Art</h3>
        <p class="price">₹5000</p>
    </div>
</div>

<div class="artwork-card">
    <div class="artwork-image">
    <img src="image/warli.jpg" alt="Warli Art">
        <div class="overlay">
            <a class="wishlist-btn" href="wishlist.php?title=Warli%20Art&price=1000&image=image/warli.jpg" style="text-decoration:none;">♡</a>
            <button class="cart-btn">BUY NOW</button>
        </div>
    </div>
    <div class="artwork-info">
        <h3>Warli Art</h3>
        <p class="price">₹1000</p>
    </div>   
</div>

<div class="artwork-card">
    <div class="artwork-image">
    <img src="image/dotmandala.jpg" alt="Dot Mandala Art">
        <div class="overlay">
            <a class="wishlist-btn" href="wishlist.php?title=dotmandala%20Art&price=1500&image=image/dotmandala.jpg" style="text-decoration:none;">♡</a>
            <button class="cart-btn">BUY NOW</button>
        </div>
    </div>
    <div class="artwork-info">
        <h3>Dot Mandala Art</h3>
        <p class="price">₹1500</p>
    </div>
</div>

<div class="artwork-card">
    <div class="artwork-image">
    <img src="image/resin.jpg" alt="Resin Art">
        <div class="overlay">
            <a class="wishlist-btn" href="wishlist.php?title=Resin%20Art&price=3000&image=image/resin.jpg" style="text-decoration:none;">♡</a>
            <button class="cart-btn">BUY NOW</button>
        </div>
    </div>
    <div class="artwork-info">
        <h3>Resin Art</h3>
        <p class="price">₹3000</p>
    </div>
</div>    

<div class="artwork-card">
    <div class="artwork-image">
    <img src="image/acrylic.jpg" alt="Acrylic Art">
        <div class="overlay">
            <a class="wishlist-btn" href="wishlist.php?title=Acrylic%20Art&price=2500&image=image/acrylic.jpg" style="text-decoration:none;">♡</a>
            <button class="cart-btn">BUY NOW</button>
        </div>
    </div>
    <div class="artwork-info">
        <h3>Acrylic Art</h3>
        <p class="price">₹2500</p>
    </div>
</div>    

<div class="artwork-card">
    <div class="artwork-image">
    <img src="image/watercolor.jpg" alt="Watercolor Art">
        <div class="overlay">
            <a class="wishlist-btn" href="wishlist.php?title=Watercolor%20Art&price=1200&image=image/watercolor.jpg" style="text-decoration:none;">♡</a>
            <button class="cart-btn">BUY NOW</button>
        </div>
    </div>
    <div class="artwork-info">
        <h3>Watercolor Art</h3>
        <p class="price">₹1200</p>
    </div>
</div>    

<div class="artwork-card">
    <div class="artwork-image">
    <img src="image/pista.jpg" alt="Pista Art">
        <div class="overlay">
            <a class="wishlist-btn" href="wishlist.php?title=Oil%20Painting&price=1000&image=image/pista.jpg" style="text-decoration:none;">♡</a>
            <button class="cart-btn">BUY NOW</button>
        </div>
    </div>
    <div class="artwork-info">
        <h3>Pista Art</h3>
        <p class="price">₹1000</p>
    </div>
</div>

<div class="artwork-card">
    <div class="artwork-image">
    <img src="image/colorfulllipana.jpg" alt="Colorfull Lipan">
        <div class="overlay">
            <a class="wishlist-btn" href="wishlist.php?title=Colorfull%20Lipan&price=2000&image=image/colorfulllipan.jpg" style="text-decoration:none;">♡</a>
            <button class="cart-btn">BUY NOW</button>
        </div>
    </div>
    <div class="artwork-info">
        <h3>Colorfull Lipan</h3>
        <p class="price">₹2000</p>
    </div>
</div>

</div>

</div>

<footer class="bg-light mt-5 py-4">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-8 text-md-start text-center mb-2 mb-md-0">
        <div class="footer-links">
          <a href="home.php">Home</a>
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