<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Terms & Conditions - Vibe Art Gallery</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="style.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Georgia', serif;
      line-height: 1.8;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      color: #333;
    }

    .main-container {
      max-width: 900px;
      margin: 120px auto 50px;
      background: white;
      padding: 60px;
      border-radius: 15px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    }
    
    .header {
      text-align: center;
      margin-bottom: 50px;
      padding-bottom: 30px;
      border-bottom: 3px solid #ffe082;
    }

    .header h1 {
      color: #764ba2;
      font-size: 42px;
      margin-bottom: 10px;
      font-weight: 700;
    }

    .last-updated {
      color: #666;
      font-style: italic;
      font-size: 14px;
    }

    .section {
      margin-bottom: 40px;
    }

    .section h2 {
      color: #667eea;
      font-size: 26px;
      margin-bottom: 15px;
      padding-left: 15px;
      border-left: 4px solid #ffe082;
    }

    .section p {
      color: #555;
      margin-bottom: 15px;
      font-size: 16px;
    }

    .section ul {
      margin-left: 40px;
      margin-bottom: 15px;
    }

    .section li {
      color: #555;
      margin-bottom: 10px;
      font-size: 16px;
    }
    
    .art-notice {
      background: linear-gradient(135deg, #fff8e1, #ffe082);
      padding: 25px;
      border-radius: 10px;
      margin: 30px 0;
      border-left: 5px solid #764ba2;
    }

    .art-notice h3 {
      color: #764ba2;
      margin-bottom: 10px;
      font-size: 20px;
    }

    .art-notice p {
      color: #333;
    }

    .important-notice {
      background: #ffebee;
      border-left: 5px solid #e53935;
      padding: 20px;
      border-radius: 8px;
      margin: 25px 0;
    } 
    .important-notice p {
     color: black; 
     background: #ffebee;
     font-weight: 600; 
    }

    .important-notice h3 {
      color: #e53935;
      margin-bottom: 10px;
    }

    .contact-box {
      background: #f5f5f5;
      padding: 30px;
      border-radius: 10px;
      text-align: center;
      margin-top: 40px;
    }

    .contact-box h2 {
      color: #764ba2;
      margin-bottom: 15px;
    }

    .contact-box p {
      font-size: 16px;
      color: #555;
    }

    .contact-box a {
      color: #667eea;
      text-decoration: none;
      font-weight: bold;
    }

    .contact-box a:hover {
      text-decoration: underline;
    }

    .social-links {
      margin-top: 20px;
    }

    .social-links a {
      display: inline-block;
      margin: 0 15px;
      color: #667eea;
      text-decoration: none;
      font-weight: 500;
      transition: transform 0.3s;
    }

    .social-links a:hover {
      transform: translateY(-3px);
      color: #764ba2;
    }

    .policy-footer {
      text-align: center;
      padding: 30px;
      color: white;
      font-size: 14px;
    }

    .policy-footer a {
      color: #ffe082;
      text-decoration: none;
    }

    .back-to-top {
      position: fixed;
      bottom: 30px;
      right: 30px;
      background: #764ba2;
      color: white;
      padding: 15px 20px;
      border-radius: 50px;
      text-decoration: none;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
      transition: all 0.3s;
    }

    .back-to-top:hover {
      background: #667eea;
      transform: translateY(-3px);
    }

    @media (max-width: 768px) {
      .main-container {
        padding: 30px 20px;
        margin: 120px 15px 30px;
      }

      .header h1 {
        font-size: 32px;
      }
    }
  </style>
</head>
<body>

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

  <div class="main-container">
    <div class="header">
      <h1>Terms & Conditions</h1>
      <p class="last-updated">Last Updated: October 4, 2025</p>
    </div>

    <div class="section">
      <p>Welcome to <strong>Vibe Art Gallery</strong>. By accessing or using our website, viewing our artwork collections, or purchasing art pieces, you agree to comply with and be bound by the following terms and conditions. Please read them carefully.</p>
    </div>

    <div class="art-notice">
      <h3>🎨 Art Gallery Agreement</h3>
      <p>These terms govern your use of our art gallery services, including viewing exhibitions, purchasing artwork, commissioning custom pieces, and participating in gallery events.</p>
    </div>

    <div class="section">
      <h2>Use of Website</h2>
      <ul>
        <li>You must be at least 18 years old to make purchases or enter into contracts through our website</li>
        <li>You agree to use the website only for lawful purposes related to art appreciation and purchase</li>
        <li>You may not attempt to disrupt, damage, or interfere with the website's functionality</li>
        <li>You may not use automated systems (bots, scrapers) to access or collect information from our site</li>
        <li>You agree not to reproduce, duplicate, or copy any artwork images without express permission</li>
        <li>Gallery visit reservations and event registrations are subject to availability</li>
      </ul>
    </div>

    <div class="section">
      <h2>Intellectual Property & Copyright</h2>
      <p><strong>All artwork, images, photographs, text, graphics, and materials on this website are protected by copyright laws and are the exclusive property of the artists and Vibe Art Gallery.</strong></p>
      <ul>
        <li><strong>Artwork Copyright:</strong> Each artwork remains the intellectual property of the artist until purchased</li>
        <li><strong>Image Protection:</strong> Artwork images may not be downloaded, reproduced, distributed, or used for commercial purposes without written permission</li>
        <li><strong>Personal Use:</strong> Images may be viewed for personal reference only</li>
        <li><strong>Watermarks:</strong> All displayed images contain digital watermarks for protection</li>
        <li><strong>Artist Rights:</strong> Artists retain moral rights to their work even after sale</li>
        <li><strong>Website Content:</strong> All text, design elements, and website layout are copyrighted</li>
      </ul>
    </div>

    <div class="important-notice">
      <h3>⚠️ Unauthorized Use Warning</h3>
      <p>Unauthorized reproduction, distribution, or commercial use of artwork images is strictly prohibited and may result in legal action. We actively monitor for copyright infringement.</p>
    </div>

    <div class="section">
      <h2>Artwork Purchases & Sales</h2>
      <ul>
        <li><strong>Authenticity:</strong> All artwork sold is guaranteed authentic and comes with a certificate of authenticity</li>
        <li><strong>Pricing:</strong> Prices are subject to change without notice. The price at checkout is the final price</li>
        <li><strong>Payment:</strong> We accept major credit cards and secure payment methods. Payment is due at time of purchase</li>
        <li><strong>Availability:</strong> Artwork is sold on a first-come, first-served basis. We reserve the right to cancel orders if items become unavailable</li>
        <li><strong>Shipping:</strong> Shipping costs and timeframes vary by location. Artwork is insured during transit</li>
        <li><strong>Return Policy:</strong> Returns accepted within 14 days if artwork arrives damaged or not as described</li>
        <li><strong>Commissions:</strong> Custom artwork commissions require a 50% non-refundable deposit</li>
      </ul>
    </div>

    <div class="section">
      <h2>Artist Representations</h2>
      <ul>
        <li>We represent artists and showcase their work with their explicit permission</li>
        <li>Artist biographies and statements are provided as submitted by the artists</li>
        <li>We facilitate connections between buyers and artists but do not guarantee availability for commissions</li>
        <li>All artwork authenticity is verified before listing</li>
      </ul>
    </div>

    <div class="section">
      <h2>Gallery Events & Exhibitions</h2>
      <ul>
        <li>Exhibition dates and featured artists are subject to change</li>
        <li>Event registrations are non-transferable unless otherwise stated</li>
        <li>We reserve the right to refuse entry or cancel events due to capacity or safety concerns</li>
        <li>Photography during events may be restricted to protect artist copyright</li>
        <li>By attending events, you consent to being photographed for gallery promotional use</li>
      </ul>
    </div>

    <div class="section">
      <h2>User Accounts</h2>
      <ul>
        <li>You are responsible for maintaining the confidentiality of your account credentials</li>
        <li>You must provide accurate and complete information when creating an account</li>
        <li>You agree to notify us immediately of any unauthorized use of your account</li>
        <li>We reserve the right to suspend or terminate accounts that violate these terms</li>
      </ul>
    </div>

    <div class="section">
      <h2>Limitation of Liability</h2>
      <p>While we strive to provide accurate information and quality service:</p>
      <ul>
        <li>We are not responsible for any indirect, incidental, or consequential damages</li>
        <li>Artwork colors may appear differently on various screens and devices</li>
        <li>We do not guarantee artwork will appreciate in value</li>
        <li>We are not liable for delays caused by shipping carriers or customs</li>
        <li>Our total liability is limited to the purchase price of the artwork</li>
      </ul>
    </div>

    <div class="section">
      <h2>Third-Party Links & Services</h2>
      <ul>
        <li>Our website may contain links to artist websites, social media, or payment processors</li>
        <li>We are not responsible for the content, privacy practices, or services of third-party sites</li>
        <li>External links are provided for convenience and do not constitute endorsement</li>
      </ul>
    </div>

    <div class="section">
      <h2>Privacy & Data Protection</h2>
      <p>Your privacy is important to us. Please review our <a href="privacy.php" style="color: #667eea; text-decoration: none; font-weight: bold;">Privacy Policy</a> to understand how we collect, use, and protect your personal information.</p>
    </div>

    <div class="section">
      <h2>Dispute Resolution</h2>
      <ul>
        <li>We encourage direct communication to resolve any issues or concerns</li>
        <li>Any disputes will first be addressed through good-faith negotiation</li>
        <li>If resolution cannot be reached, disputes may be subject to binding arbitration</li>
      </ul>
    </div>

    <div class="section">
      <h2>Changes to Terms</h2>
      <p>We reserve the right to modify these Terms & Conditions at any time to reflect changes in our services, legal requirements, or business practices. Material changes will be communicated via:</p>
      <ul>
        <li>Email notification to registered users</li>
        <li>Prominent notice on our website</li>
        <li>Updated "Last Updated" date at the top of this page</li>
      </ul>
      <p>Continued use of our website after changes indicates acceptance of the updated terms.</p>
    </div>

    <div class="section">
      <h2>Severability</h2>
      <p>If any provision of these terms is found to be unenforceable or invalid, the remaining provisions will continue in full force and effect.</p>
    </div>

    <div class="section">
      <h2>Entire Agreement</h2>
      <p>These Terms & Conditions, along with our Privacy Policy, constitute the entire agreement between you and Vibe Art Gallery regarding the use of our website and services.</p>
    </div>
    
    <div class="contact-box">
      <h2>Questions or Concerns?</h2>
      <p>If you have any questions about these Terms & Conditions, artwork purchases, or our gallery services, please don't hesitate to contact us:</p>
      <p><strong>Email:</strong> <a href="mailto:ghediyadrashti2@gmail.com">ghediyadrashti2@gmail.com</a></p>
      <p><strong>Artist & Gallery Owner:</strong> Drashti Ghediya</p>
      
      <div class="social-links">
        <a href="https://instagram.com/art_by_drashti__" target="_blank">📷 Instagram: @art_by_drashti__</a>
        <a href="https://youtube.com/@drashtimandalart" target="_blank">🎥 YouTube: Drashti's Mandala Art</a>
      </div>
      
      <p style="margin-top: 20px; font-size: 14px; color: #777;">We typically respond within 24-48 hours</p>
    </div>
  </div>

  <div class="policy-footer">
    <p>&copy; 2025 Vibe Art Gallery. All rights reserved. | <a href="privacy.php">Privacy Policy</a></p>
  </div>
  
  <a href="#" class="back-to-top">↑ Top</a>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>