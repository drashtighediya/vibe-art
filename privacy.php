<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Privacy Policy - Vibe Art Gallery</title>
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

    /* Main Container */
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
      <h1>Privacy Policy</h1>
      <p class="last-updated">Last Updated: October 4, 2025</p>
    </div>

    <div class="section">
      <p>At <strong>Vibe Art Gallery</strong>, we are dedicated to protecting your privacy and ensuring the security of your personal information. This Privacy Policy outlines how we collect, use, and safeguard your data when you visit our art gallery website or interact with our services.</p>
    </div>

    <div class="art-notice">
      <h3>🖼️ Art Gallery Commitment</h3>
      <p>As an art gallery, we respect the creative rights and privacy of both our artists and visitors. All artwork images, artist information, and exhibition details are handled with the utmost care and confidentiality.</p>
    </div>

    <div class="section">
      <h2>Information We Collect</h2>
      <p>We may collect the following types of information:</p>
      <ul>
        <li><strong>Personal Information:</strong> Name, email address, phone number, and mailing address (when you sign up for newsletters, purchase artwork, or contact us)</li>
        <li><strong>Artwork Preferences:</strong> Information about artworks you view, favorite, or inquire about</li>
        <li><strong>Transaction Data:</strong> Payment information and purchase history (processed securely through third-party payment processors)</li>
        <li><strong>Technical Data:</strong> IP address, browser type, device information, and website usage patterns</li>
        <li><strong>Gallery Visit Information:</strong> Exhibition attendance and event registrations</li>
      </ul>
    </div>

    <div class="section">
      <h2>How We Use Your Information</h2>
      <p>Your information helps us provide a better art gallery experience:</p>
      <ul>
        <li>Process artwork purchases and inquiries</li>
        <li>Send updates about new exhibitions, artists, and gallery events</li>
        <li>Personalize your art browsing experience</li>
        <li>Improve our website functionality and user interface</li>
        <li>Respond to your questions and provide customer support</li>
        <li>Manage artist commissions and custom artwork requests</li>
        <li>Analyze visitor trends to curate better exhibitions</li>
      </ul>
    </div>

    <div class="section">
      <h2>Artist Privacy & Intellectual Property</h2>
      <p>We take special care to protect our artists' information and creative works:</p>
      <ul>
        <li>Artist contact information is kept confidential unless permission is granted</li>
        <li>All artwork images are watermarked and copyright-protected</li>
        <li>Artist portfolios and statements are published only with explicit consent</li>
        <li>We never share artist sales data with third parties</li>
      </ul>
    </div>

    <div class="section">
      <h2>Data Protection & Security</h2>
      <p>We implement industry-standard security measures to protect your personal data:</p>
      <ul>
        <li>Secure SSL encryption for all data transmission</li>
        <li>Regular security audits and updates</li>
        <li>Restricted access to personal information (authorized personnel only)</li>
        <li>Secure payment processing through PCI-compliant providers</li>
        <li>Regular backups to prevent data loss</li>
      </ul>
    </div>

    <div class="section">
      <h2>Cookies & Tracking</h2>
      <p>Our website uses cookies to enhance your browsing experience:</p>
      <ul>
        <li><strong>Essential Cookies:</strong> Required for website functionality</li>
        <li><strong>Analytics Cookies:</strong> Help us understand visitor behavior and improve our gallery</li>
        <li><strong>Preference Cookies:</strong> Remember your settings and favorites</li>
      </ul>
      <p>You can control cookie settings through your browser preferences.</p>
    </div>

    <div class="section">
      <h2>Third-Party Services</h2>
      <p>We may use trusted third-party services for:</p>
      <ul>
        <li>Payment processing (Stripe, PayPal)</li>
        <li>Email marketing (MailChimp, Constant Contact)</li>
        <li>Website analytics (Google Analytics)</li>
        <li>Social media integration</li>
      </ul>
      <p>These services have their own privacy policies, and we encourage you to review them.</p>
    </div>

    <div class="section">
      <h2>Your Rights</h2>
      <p>You have the right to:</p>
      <ul>
        <li>Access your personal data</li>
        <li>Request corrections to your information</li>
        <li>Delete your account and associated data</li>
        <li>Opt-out of marketing communications</li>
        <li>Export your data in a portable format</li>
        <li>Object to data processing</li>
      </ul>
    </div>

    <div class="section">
      <h2>Children's Privacy</h2>
      <p>Our gallery is open to all ages, but we do not knowingly collect personal information from children under 13 without parental consent. If you believe we have inadvertently collected such information, please contact us immediately.</p>
    </div>

    <div class="section">
      <h2>Changes to This Policy</h2>
      <p>We may update this Privacy Policy periodically to reflect changes in our practices or legal requirements. We will notify you of significant changes via email or website notification. The "Last Updated" date at the top indicates when changes were made.</p>
    </div>
    <div class="contact-box">
      <h2>Contact Us</h2>
      <p>If you have any questions about this Privacy Policy or how we handle your data, please reach out:</p>
      <p><strong>Email:</strong> <a href="mailto:ghediyadrashti2@gmail.com">ghediyadrashti2@gmail.com</a></p>
      <p><strong>Artist:</strong> Drashti Ghediya</p>
      
      <div class="social-links">
        <a href="https://instagram.com/art_by_drashti__" target="_blank">📷 Instagram: @art_by_drashti__</a>
        <a href="https://youtube.com/@drashtimandalart" target="_blank">🎥 YouTube: Drashti's Mandala Art</a>
      </div>
    </div>
  </div>
  <div class="policy-footer">
    <p>&copy; 2025 Vibe Art Gallery. All rights reserved. | <a href="terms.php">Terms & Conditions</a></p>
  </div>
  <a href="#" class="back-to-top">↑ Top</a>

  <script src="https://cdn.jsdelivr.net/npm