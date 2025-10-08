<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Vibe Art Gallery</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body {  
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Georgia', serif;
            color: #fff;
        }

        .logo {
            position: fixed;
            top: 10px;
            left: 20px;
            z-index: 1000;
            padding: 8px;
            background: rgba(255, 255, 255, 0.95);
            border-bottom-right-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .logo img {
            height: 20px;
        }

        .main-content {
            padding-top: 120px;
            padding-bottom: 60px;
        }

        .hero-section {
            text-align: center;
            margin-bottom: 60px;
            padding: 0 20px;
        }

        .vibeart-heading {
            font-size: 48px;
            font-weight: 700;
            color: #fff;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.3);
            margin-bottom: 24px;
        }

        .vibeart-subheading {
            font-size: 20px;
            line-height: 1.6;
            color: #f0f0f0;
            max-width: 900px;
            margin: 0 auto 30px;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.2);
        }

        .mission-section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 50px;
            margin: 40px auto;
            max-width: 1100px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            color: #333;
        }

        .mission-section h2 {
            color: #764ba2;
            font-size: 36px;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .mission-section p {
            font-size: 18px;
            line-height: 1.8;
            color: #555;
            margin-bottom: 15px;
        }

        .artist-section {
            background: linear-gradient(white, #f9f9f9);
            border-radius: 20px;
            padding: 50px;
            margin: 40px auto;
            max-width: 1100px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            color: #333;
        }

        .artist-section h2 {
            color: #764ba2;
            font-size: 36px;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .artist-info {
            display: flex;
            align-items: center;
            gap: 30px;
            margin-top: 30px;
        }

        .artist-details {
            flex: 1;
        }

        .artist-details h3 {
            color: #667eea;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .artist-details p {
            font-size: 17px;
            line-height: 1.7;
            color: #444;
        }

        .social-links {
            margin-top: 20px;
        }

        .social-links a {
            display: inline-block;
            margin-right: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .social-links a:hover {
            color: #764ba2;
            transform: translateY(-2px);
        }

        .art-categories-section {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .section-title {
            text-align: center;
            font-size: 40px;
            color: #764ba2;
            font-weight: 600;
            margin-bottom: 50px;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.3);
        }

        .art-category {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .art-category:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.25);
        }

        .art-category h3 {
            color: #667eea;
            font-size: 26px;
            margin-bottom: 15px;
            font-weight: 700;
            border-left: 4px solid #857954ff;
            padding-left: 15px;
        }

        .art-category p {
            color: #555;
            font-size: 17px;
            line-height: 1.7;
            margin: 0;
        }

        .view-artwork-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px 40px;
            font-size: 18px;
            font-weight: 600;
            border: none;
            border-radius: 50px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            margin-top: 40px;
        }

        .view-artwork-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.3);
            color: white;
        }

        .cta-section {
            text-align: center;
            padding: 60px 20px;
        }

        @media (max-width: 768px) {
            .vibeart-heading {
                font-size: 36px;
            }

            .vibeart-subheading {
                font-size: 18px;
            }

            .mission-section, .artist-section {
                padding: 30px 20px;
            }

            .artist-info {
                flex-direction: column;
            }

            .section-title {
                font-size: 32px;
            }

            .art-category h3 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="logo">
        <a href="home.php">
            <img src="image/logo.jpg" alt="Art Gallery Logo" style="height: 50px;">
        </a>
    </div>

    <div class="main-content">
        <div class="hero-section">
            <h1 class="vibeart-heading">About Vibe Art Gallery</h1>
            <p class="vibeart-subheading">
                VIBE Art Gallery operates at the intersection of creativity, culture, and belonging. 
                We believe art should be accessible to everyone, celebrating the beauty of traditional 
                and contemporary Indian art forms while fostering a vibrant artistic community.
            </p>
        </div>
        <div class="mission-section">
            <h2>Our Mission</h2>
            <p>
                At Vibe Art Gallery, we are passionate about preserving and promoting the rich heritage 
                of Indian art while embracing modern artistic expressions. Our mission is to create a 
                platform where artists can showcase their talent and art lovers can discover unique, 
                handcrafted masterpieces that tell stories, evoke emotions, and add beauty to everyday life.
            </p>
            <p>
                We specialize in traditional art forms like Mandala, Lippan, and Warli art, alongside 
                contemporary styles including Resin, Acrylic, and Dot Mandala art. Each piece in our 
                collection is carefully curated to ensure authenticity, quality, and artistic excellence.
            </p>
            <p>
                Whether you're an art collector, interior designer, or someone looking to add a personal 
                touch to your space, Vibe Art Gallery offers a diverse collection that bridges tradition 
                and innovation.
            </p>
        </div>
        <div class="artist-section">
            <a>Meet the Artist</h2>
            <div class="artist-info">
                <div class="artist-details">
                    <h3>Drashti Ghediya - Founder & Artist</h3>
                    <p>
                        Drashti Ghediya is the creative force behind Vibe Art Gallery. With a deep passion 
                        for traditional Indian art forms and a vision to make art accessible to all, Drashti 
                        has dedicated herself to preserving cultural heritage while exploring contemporary 
                        artistic techniques.
                    </p>
                    <p>
                        Specializing in Mandala art, Lippan work, and mixed media creations, Drashti's work 
                        reflects a perfect blend of intricate craftsmanship and vibrant creativity. Her journey 
                        as an artist is driven by the belief that art has the power to connect people, tell 
                        stories, and transform spaces.
                    </p>
                    <div class="social-links">
                        <a href="https://instagram.com/art_by_drashti__" target="_blank">Instagram: @art_by_drashti__</a>
                        <a href="https://youtube.com/@drashtimandalart" target="_blank">YouTube: Drashti's Mandala Art</a>
                    </div>
                    <p style="margin-top: 15px;">
                        <strong>Contact:</strong> <a href="mailto:ghediyadrashti2@gmail.com" style="color: #667eea;">ghediyadrashti2@gmail.com</a>
                    </p>
                </div>
            </div>
        </div>
        <div class="art-categories-section">
            <h2 class="section-title">Explore Our Art Forms</h2>

            <div class="art-category" id="mandala">
                <h3>Mandala Art</h3>
                <p>
                    Mandala art is a sacred, symbolic art form characterized by intricate geometric and 
                    symmetrical circular designs that originate from Hindu and Buddhist traditions. Each 
                    mandala represents the universe, balance, and spiritual journey, making it a powerful 
                    meditation tool and stunning decorative piece.
                </p>
            </div>

            <div class="art-category" id="lippan">
                <h3>Lippan Art</h3>
                <p>
                    Lippan Art is a traditional mud and mirror work from Kutch, Gujarat, known for its 
                    intricate patterns and rustic beauty. This ancient craft uses clay relief work adorned 
                    with small mirrors to create stunning wall decorations that reflect light and add 
                    traditional charm to any space.
                </p>
            </div>

            <div class="art-category" id="colorful-lippan">
                <h3>Colorful Lippan</h3>
                <p>
                    A contemporary take on traditional Lippan art, Colorful Lippan uses vibrant pigments 
                    and modern color palettes while maintaining the classic mirror work technique. The white 
                    mud base, traditionally mixed with cow dung or millet husk, is enhanced with bold colors 
                    to create eye-catching pieces perfect for modern interiors.
                </p>
            </div>

            <div class="art-category" id="warli">
                <h3>Warli Art</h3>
                <p>
                    Warli Art is a tribal folk art from Maharashtra, dating back to 2500 BCE. Known for 
                    its simple white geometric patterns depicting daily life, nature, and rituals, Warli 
                    art tells stories of community, celebration, and harmony with nature through minimalist 
                    yet expressive designs.
                </p>
            </div>

            <div class="art-category" id="dot-mandala">
                <h3>Dot Mandala Art</h3>
                <p>
                    Dot Mandala Art is a contemporary meditative art form that uses intricate dot patterns 
                    to create mesmerizing circular designs. Each dot is carefully placed to form hypnotic 
                    patterns that symbolize unity, balance, and the infinite nature of the universe. Perfect 
                    for modern spaces seeking a blend of spirituality and contemporary aesthetics.
                </p>
            </div>

            <div class="art-category" id="resin">
                <h3>Resin Art</h3>
                <p>
                    Resin Art is a modern art form using epoxy resin to create glossy, vibrant, and often 
                    three-dimensional artworks. Known for its depth, fluidity, and glass-like finish, resin 
                    art can incorporate various materials like pigments, glitter, and natural elements to 
                    create stunning contemporary pieces.
                </p>
            </div>

            <div class="art-category" id="acrylic">
                <h3>Acrylic Art</h3>
                <p>
                    Acrylic Art is a versatile and fast-drying art form using acrylic paints to create 
                    vibrant, bold, and textured artworks. The medium allows for various techniques from 
                    smooth gradients to thick impasto, making it ideal for contemporary abstract and 
                    realistic compositions on canvas and other surfaces.
                </p>
            </div>

            <div class="art-category" id="watercolor">
                <h3>Watercolor Art</h3>
                <p>
                    Watercolor Art is a delicate and translucent art form using water-based paints to 
                    create soft, flowing, and ethereal artworks on paper. The medium's transparency and 
                    unpredictability create unique effects, perfect for landscapes, florals, and 
                    impressionistic pieces that capture light and emotion.
                </p>
            </div>

            <div class="art-category" id="oil">
                <h3>Oil Paintings</h3>
                <p>
                    Oil Paintings are a classic art form using oil-based paints to create rich, textured, 
                    and long-lasting artworks. The slow-drying nature of oil paints allows for blending 
                    and layering techniques that produce depth, luminosity, and timeless beauty in 
                    traditional and contemporary compositions.
                </p>
            </div>

            <div class="art-category" id="pista">
                <h3>Pista Shell Art</h3>
                <p>
                    Pista Shell Art is an innovative eco-friendly craft that transforms discarded pistachio 
                    shells into beautiful decorative pieces. The shells are cleaned, painted, and arranged 
                    to create frames, wall hangings, flowers, and intricate designs, proving that art can 
                    be both sustainable and stunning.
                </p>
            </div>

            <div class="cta-section">
                <a href="view_orders.php" class="view-artwork-btn">View All Artworks</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>