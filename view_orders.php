<?php
session_start();
$host = 'localhost';
$dbname = 'art_gallery';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
try {
    $stmt = $pdo->query("
        SELECT p.*, 
               a.artist_name as artist_name, 
               at.type_name as art_type_name,
               am.medium_name as medium_name
        FROM product p
        LEFT JOIN artists a ON p.artist_id = a.id
        LEFT JOIN arttype at ON p.type_id = at.id
        LEFT JOIN artmedium am ON p.medium_id = am.id
        ORDER BY p.id DESC
    ");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $stmt = $pdo->query("SELECT * FROM product ORDER BY id DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Artworks - Online Art Gallery</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body.view-artwork-page {
            padding-top: 80px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .page-header {
            text-align: center;
            padding: 60px 0 40px;
        }
        
        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .artwork-description {
            font-size: 0.9rem;
            color: #666;
            line-height: 1.5;
            margin-top: 8px;
            padding: 0 10px;
            text-align: center;
        }

        .artwork-card {
            transition: transform 0.3s ease;
        }

        .artwork-card:hover {
            transform: translateY(-5px);
        }
    </style>
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

    <div class="container">
        <div class="page-header">
            <h1>Browse Our Collection</h1>
        </div>
    </div>

    <div class="artwork-row">
        <div class="artwork-card">
            <div class="artwork-image">
                <img src="image/tiger painting.jpg" alt="Tiger Painting">
                <div class="overlay">
                    <a class="wishlist-btn" href="wishlist.php?title=Tiger%20Art&price=1000&image=image/Tiger.jpg" style="text-decoration:none;">♡</a>
                    <a class="cart-btn" href="buy_now.php?title=Tiger%20Painting&price=500&image=image/tiger%20painting.jpg" style="text-decoration:none;">BUY NOW</a>
                </div>
            </div>
            <div class="artwork-info">
                <h3>Tiger Painting</h3>
                <p class="price">₹500</p>
                <p class="artwork-description">A majestic tiger portrait capturing the raw power and grace of this magnificent creature in vivid detail.</p>
            </div>
        </div>

        <div class="artwork-card">
            <div class="artwork-image">
                <img src="image/colourfull.jpg" alt="Mandala Art">
                <div class="overlay">
                    <a class="wishlist-btn" href="wishlist.php?title=Butterfly%20Art&price=1000&image=image/Colourfull.jpg" style="text-decoration:none;">♡</a>
                    <a class="cart-btn" href="buy_now.php?title=Mandala%20Art&price=2000&image=image/colourfull.jpg" style="text-decoration:none;">BUY NOW</a>
                </div>
            </div>
            <div class="artwork-info">
                <h3>Mandala Art</h3>
                <p class="price">₹2000</p>
                <p class="artwork-description">Intricate mandala design featuring vibrant colors and geometric patterns, perfect for meditation and spiritual decor.</p>
            </div>
        </div>

        <div class="artwork-card">
            <div class="artwork-image">
                <img src="image/owl.jpg" alt="Owl Art">
                <div class="overlay">
                    <a class="wishlist-btn" href="wishlist.php?title=owl%20Art&price=800&image=image/owl.jpg" style="text-decoration:none;">♡</a>
                    <a class="cart-btn" href="buy_now.php?title=Owl%20Art&price=800&image=image/owl.jpg" style="text-decoration:none;">BUY NOW</a>
                </div>
            </div>
            <div class="artwork-info">
                <h3>Owl Art</h3>
                <p class="price">₹800</p>
                <p class="artwork-description">A wise owl depicted with intricate details and expressive eyes, symbolizing wisdom and knowledge.</p>
            </div>
        </div>

        <div class="artwork-card">
            <div class="artwork-image">
                <img src="image/butterfly.jpg" alt="Butterfly Art">
                <div class="overlay">
                    <a class="wishlist-btn" href="wishlist.php?title=Butterfly%20Art&price=1000&image=image/butterfly.jpg" style="text-decoration:none;">♡</a>
                    <a class="cart-btn" href="buy_now.php?title=Butterfly%20Art&price=1000&image=image/butterfly.jpg" style="text-decoration:none;">BUY NOW</a>
                </div>
            </div>
            <div class="artwork-info">
                <h3>Butterfly Art</h3>
                <p class="price">₹1000</p>
                <p class="artwork-description">Delicate butterfly artwork with vibrant wings, representing transformation and natural beauty.</p>
            </div>
        </div>

        <div class="artwork-card">
            <div class="artwork-image">
                <img src="image/krishna.jpg" alt="Krishna Art">
                <div class="overlay">
                    <a class="wishlist-btn" href="wishlist.php?title=Krishna%20Art&price=500&image=image/Krisna.jpg" style="text-decoration:none;">♡</a>
                    <a class="cart-btn" href="buy_now.php?title=Krishna%20Art&price=500&image=image/krishna.jpg" style="text-decoration:none;">BUY NOW</a>
                </div>
            </div>
            <div class="artwork-info">
                <h3>Krishna Art</h3>
                <p class="price">₹500</p>
                <p class="artwork-description">Divine portrayal of Lord Krishna with traditional motifs, bringing spiritual serenity to your space.</p>
            </div>
        </div>

        <div class="artwork-card">
            <div class="artwork-image">
                <img src="image/lippan.jpg" alt="Lippan Art">
                <div class="overlay">
                    <a class="wishlist-btn" href="wishlist.php?title=Lippan%20Art&price=1000&image=image/Lippan.jpg" style="text-decoration:none;">♡</a>
                    <a class="cart-btn" href="buy_now.php?title=Lippan%20Art&price=1000&image=image/Lippan.jpg" style="text-decoration:none;">BUY NOW</a>
                </div>
            </div>
            <div class="artwork-info">
                <h3>Lippan Art</h3>
                <p class="price">₹1000</p>
                <p class="artwork-description">Traditional Gujarati mud and mirror work art featuring intricate patterns and cultural heritage.</p>
            </div>
        </div>
        
        <div class="artwork-card">
            <div class="artwork-image">
                <img src="image/mirrorart.jpg" alt="Mirror Art">
                <div class="overlay">
                    <a class="wishlist-btn" href="wishlist.php?title=mirror%20art&price=5000&image=image/mirrorart.jpg" style="text-decoration:none;">♡</a>
                    <a class="cart-btn" href="buy_now.php?title=mirrorart%20Art&price=5000&image=image/mirrorart.jpg" style="text-decoration:none;">BUY NOW</a>
                </div>
            </div>
            <div class="artwork-info">
                <h3>Mirror Art</h3>
                <p class="price">₹5000</p>
                <p class="artwork-description">Exquisite mirror work with elaborate designs that sparkle and shine, adding glamour to any wall.</p>
            </div>
        </div>    
            
        <div class="artwork-card">
            <div class="artwork-image">
                <img src="image/warli.jpg" alt="Warli Art">
                <div class="overlay">
                    <a class="wishlist-btn" href="wishlist.php?title=Warli%20Art&price=1000&image=image/warli.jpg" style="text-decoration:none;">♡</a>
                    <a class="cart-btn" href="buy_now.php?title=Warli%20Art&price=1000&image=image/warli.jpg" style="text-decoration:none;">BUY NOW</a>
                </div>
            </div>
            <div class="artwork-info">
                <h3>Warli Art</h3>
                <p class="price">₹1000</p>
                <p class="artwork-description">Ancient tribal art form depicting daily life scenes with simple geometric shapes and earthy tones.</p>
            </div>   
        </div>

        <div class="artwork-card">
            <div class="artwork-image">
                <img src="image/dotmandala.jpg" alt="Dot Mandala Art">
                <div class="overlay">
                    <a class="wishlist-btn" href="wishlist.php?title=dotmandala%20Art&price=1500&image=image/dotmandala.jpg" style="text-decoration:none;">♡</a>
                    <a class="cart-btn" href="buy_now.php?title=dotmandala%20Art&price=1500&image=image/dotmandala.jpg" style="text-decoration:none;">BUY NOW</a>
                </div>
            </div>
            <div class="artwork-info">
                <h3>Dot Mandala Art</h3>
                <p class="price">₹1500</p>
                <p class="artwork-description">Mesmerizing dot painting technique creating stunning mandala patterns with precision and patience.</p>
            </div>
        </div>

        <div class="artwork-card">
            <div class="artwork-image">
                <img src="image/resin.jpg" alt="Resin Art">
                <div class="overlay">
                    <a class="wishlist-btn" href="wishlist.php?title=Resin%20Art&price=3000&image=image/resin.jpg" style="text-decoration:none;">♡</a>
                    <a class="cart-btn" href="buy_now.php?title=Resin%20Art&price=3000&image=image/resin.jpg" style="text-decoration:none;">BUY NOW</a>
                </div>
            </div>
            <div class="artwork-info">
                <h3>Resin Art</h3>
                <p class="price">₹3000</p>
                <p class="artwork-description">Contemporary resin artwork with fluid designs and glossy finish, creating depth and movement.</p>
            </div>
        </div> 

        <div class="artwork-card">
            <div class="artwork-image">
                <img src="image/acrylic.jpg" alt="Acrylic Art">
                <div class="overlay">
                    <a class="wishlist-btn" href="wishlist.php?title=Acrylic%20Art&price=2000&image=image/acrylic.jpg" style="text-decoration:none;">♡</a>
                    <a class="cart-btn" href="buy_now.php?title=Acrylic%20Art&price=2000&image=image/acrylic.jpg" style="text-decoration:none;">BUY NOW</a>
                </div>
            </div>
            <div class="artwork-info">
                <h3>Acrylic Art</h3>
                <p class="price">₹2000</p>
                <p class="artwork-description">Bold acrylic painting with rich colors and expressive brushstrokes, adding energy to your space.</p>
            </div>
        </div>

        <div class="artwork-card">
            <div class="artwork-image">
                <img src="image/watercolor.jpg" alt="Watercolor Art">
                <div class="overlay">
                    <a class="wishlist-btn" href="wishlist.php?title=Watercolor%20Art&price=1500&image=image/watercolor.jpg" style="text-decoration:none;">♡</a>
                    <a class="cart-btn" href="buy_now.php?title=Watercolor%20Art&price=1500&image=image/watercolor.jpg" style="text-decoration:none;">BUY NOW</a>
                </div>
            </div>
            <div class="artwork-info">
                <h3>Watercolor Art</h3>
                <p class="price">₹1500</p>
                <p class="artwork-description">Soft watercolor painting with delicate washes and translucent layers creating ethereal beauty.</p>
            </div>
        </div>    

        <div class="artwork-card">
            <div class="artwork-image">
                <img src="image/oil painting.jpg" alt="Oil Painting">
                <div class="overlay">
                    <a class="wishlist-btn" href="wishlist.php?title=Oil%20Painting&price=1000&image=image/oil painting.jpg" style="text-decoration:none;">♡</a>
                    <a class="cart-btn" href="buy_now.php?title=Oil%20Painting&price=1000&image=image/oil painting.jpg" style="text-decoration:none;">BUY NOW</a>
                </div>
            </div>
            <div class="artwork-info">
                <h3>Oil Painting</h3>
                <p class="price">₹1000</p>
                <p class="artwork-description">Classic oil painting technique with rich textures and timeless appeal for art connoisseurs.</p>
            </div>
        </div>

        <div class="artwork-card">
            <div class="artwork-image">
                <img src="image/pista.jpg" alt="Pista Art">
                <div class="overlay">
                    <a class="wishlist-btn" href="wishlist.php?title=Oil%20Painting&price=1000&image=image/pista.jpg" style="text-decoration:none;">♡</a>
                    <a class="cart-btn" href="buy_now.php?title=pista%20Art&price=1000&image=image/pista.jpg" style="text-decoration:none;">BUY NOW</a>
                </div>
            </div>
            <div class="artwork-info">
                <h3>Pista Art</h3>
                <p class="price">₹1000</p>
                <p class="artwork-description">Creative artwork made from pistachio shells, showcasing innovative use of natural materials.</p>
            </div>
        </div>

        <div class="artwork-card">
            <div class="artwork-image">
                <img src="image/colorfulllipan.jpg" alt="Colorfull Lipan">
                <div class="overlay">
                    <a class="wishlist-btn" href="wishlist.php?title=Colorfull%20Lipan&price=2000&image=image/colorfulllipan.jpg" style="text-decoration:none;">♡</a>
                    <a class="cart-btn" href="buy_now.php?title=colorfulllipan%20Art&price=2000&image=image/colorfulllipan.jpg" style="text-decoration:none;">BUY NOW</a>
                </div>
            </div>
            <div class="artwork-info">
                <h3>Colorfull Lipan</h3>
                <p class="price">₹2000</p>
                <p class="artwork-description">Vibrant Lippan art with colorful clay work and mirror embellishments, celebrating Gujarat's rich heritage.</p>
            </div>
        </div>
        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $product): ?>
            <div class="artwork-card">
                <div class="artwork-image">
                    <?php if (!empty($product['image'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['title']); ?>">
                    <?php else: ?>
                        <img src="image/placeholder.jpg" alt="No Image">
                    <?php endif; ?>
                    <div class="overlay">
                        <?php 
                        $imagePath = !empty($product['image']) ? 'uploads/' . $product['image'] : 'image/placeholder.jpg';
                        $wishlistUrl = sprintf(
                            'wishlist.php?title=%s&price=%s&image=%s',
                            urlencode($product['title']),
                            urlencode($product['price']),
                            urlencode($imagePath)
                        );
                        $buyUrl = sprintf(
                            'buy_now.php?title=%s&price=%s&image=%s',
                            urlencode($product['title']),
                            urlencode($product['price']),
                            urlencode($imagePath)
                        );
                        ?>
                        <a class="wishlist-btn" href="<?php echo $wishlistUrl; ?>" style="text-decoration:none;">♡</a>
                        <a class="cart-btn" href="<?php echo $buyUrl; ?>" style="text-decoration:none;">BUY NOW</a>
                    </div>
                </div>
                <div class="artwork-info">
                    <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                    <p class="price">₹<?php echo number_format($product['price'], 2); ?></p>
                    <?php if (!empty($product['description'])): ?>
                    <p class="artwork-description"><?php echo htmlspecialchars($product['description']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <footer class="bg-light mt-5 py-4">
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