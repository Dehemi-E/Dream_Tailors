<?php
/**
 * Dream Tailors - S22 Ultra Optimized
 */
$site_title = "Dream Tailors - Premium Bespoke Tailoring";
$header_logo_text = "Dream Tailors";
$hero_large_text = "Dream Tailors";
// Hero subtitle (small text) removed as requested
$hero_gent_text = "Tailored for Perfection";

$hours_title = "WORKING HOURS";
$hours_desc = "Premium tailored measurements and styling consultation sessions available within operational workshop timings.";
$hours_days = [
    "Monday - Friday" => "9am - 6pm",
    "Saturday"         => "9am - 1pm",
    "Sunday"           => "Closed"
];

$contact_phone = "072 970 0050";
$contact_whatsapp = "076 606 6556";

$quality_tagline = "Only The Best Materials";
$quality_title = "AMAZING QUALITY";

$gallery_items = [
    ['https://images.unsplash.com/photo-1594938298603-c8148c4dae35?auto=format&fit=crop&q=80&w=600', 'Elegant Men\'s Blazer Setup'],
    ['https://images.unsplash.com/photo-1617137984095-74e4e5e3613f?auto=format&fit=crop&q=80&w=600', 'Men\'s Formal Styling'],
    ['https://images.unsplash.com/photo-1598808503746-f34c53b9323e?auto=format&fit=crop&q=80&w=600', 'Ladies Tailored Tux'],
    ['https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&q=80&w=600', 'Cuff and Hands Detail'],
    ['https://media.istockphoto.com/id/1657460312/photo/beautiful-sensual-woman.jpg?s=612x612&w=0&k=20&c=B63DnzI-bk1xKNKXYtzyRMyi0Cu0crlZNYQ1D5CkxKk=', 'Fine Fabric Selection'],
    ['https://images.unsplash.com/photo-1487222477894-8943e31ef7b2?auto=format&fit=crop&q=80&w=600', 'Womenswear Tailoring Fitting'],
    ['https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRhwROtQhQf7k5R0b3H0S-4ibxIbKVQ_xbiebTHxHZwdA&s=10', 'Childrenswear Pattern Making'],
    ['https://images.unsplash.com/photo-1585487000160-6ebcfceb0d03?auto=format&fit=crop&q=80&w=600', 'Children\'s Formal Vest & Jacket']
];

$testimonial_quote = "Perfect fit and excellent quality material. The tailoring process was professional and I am absolutely delighted with the results. Highly recommend Dream Tailors.";
$testimonial_author = "BY DAN PARKS";

$contact_section_title = "CONTACT ME";
$contact_email = "info@dreamtailors.com";
$contact_phone_number = "072 970 0050";
$contact_address = "Main Street, Kurunegala";

$footer_brand_logo = "Dream<span>Tailors</span>";
$footer_desc = "Three decades of excellence in bespoke tailoring, crafting unique garments for discerning clients.";
$footer_newsletter_title = "GET OUR NEWSLETTER";

$copyright_text = "© Copyright Dream Tailors";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover, target-densitydpi=device-dpi">
    <meta name="theme-color" content="#0f172a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title><?php echo $site_title; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-dark: #0f172a; 
            --accent-copper: #4f46e5; 
            --text-dark: #1e293b;
            --text-light: #ffffff;
            --text-muted: #64748b;
            --font-serif: 'Playfair Display', serif;
            --font-sans: 'Montserrat', sans-serif;
            --container-padding: clamp(16px, 4vw, 24px);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html {
            scroll-behavior: smooth;
            -webkit-tap-highlight-color: transparent;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body {
            font-family: var(--font-sans);
            color: var(--text-dark);
            background-color: #ffffff;
            line-height: 1.5;
            overflow-x: hidden;
            text-rendering: optimizeLegibility;
        }

        a { text-decoration: none; color: inherit; transition: all 0.3s ease; }
        ul { list-style: none; }
        img { width: 100%; height: auto; display: block; object-fit: cover; }

        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            padding: 0 var(--container-padding); 
        }

        /* Smooth Reveal Animation */
        .reveal-section {
            opacity: 0;
            transform: translateY(80px);
            transition: all 1.2s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: transform, opacity;
        }
        .reveal-section.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* --- HEADER --- */
        header {
            position: absolute; top: 0; left: 0; width: 100%; z-index: 1000;
            padding: clamp(15px, 3vw, 25px) 0; 
            transition: background 0.3s ease, padding 0.3s ease;
        }
        header.sticky {
            position: fixed; 
            background: rgba(15, 23, 42, 0.98); 
            padding: clamp(10px, 2vw, 15px) 0; 
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
        .nav-container { display: flex; justify-content: space-between; align-items: center; flex-wrap: nowrap; }
        .logo-wrapper { display: flex; flex-direction: row; align-items: center; gap: clamp(8px, 2vw, 12px); color: var(--text-light); flex-shrink: 0; }
        
        .logo-wrapper img { width: clamp(32px, 7vw, 40px); height: clamp(32px, 7vw, 40px); border-radius: 50%; object-fit: cover; border: 2px solid rgba(255, 255, 255, 0.4); }
        .logo { font-family: var(--font-serif); font-size: clamp(18px, 4.5vw, 24px); font-weight: 700; letter-spacing: 1px; color: var(--text-light); white-space: nowrap; transition: color 0.3s ease; }
        header.sticky .logo { color: #ffffff !important; } 
        
        .desktop-nav { display: flex; gap: clamp(10px, 3vw, 20px); align-items: center; flex-wrap: nowrap; }
        .desktop-nav li { white-space: nowrap; }
        .desktop-nav a { color: var(--text-light); font-size: clamp(10px, 2.2vw, 11px); font-weight: 600; letter-spacing: clamp(1px, 0.4vw, 2px); white-space: nowrap; }
        .desktop-nav a:hover, .desktop-nav li.active a { color: var(--accent-copper); }
        .menu-toggle { display: none; flex-direction: column; gap: clamp(4px, 1vw, 5px); cursor: pointer; z-index: 1001; }
        .menu-toggle span { width: clamp(22px, 5vw, 25px); height: 2px; background-color: var(--text-light); transition: all 0.3s ease; }

        /* Modified Auth Wrapper to be more compact */
        .auth-wrapper {
            display: flex; align-items: center;
            background: rgba(255, 255, 255, 0.05);
            padding: clamp(2px, 1vw, 4px) clamp(2px, 1vw, 4px) clamp(2px, 1vw, 4px) clamp(8px, 2vw, 12px);
            border-radius: 30px; border: 1px solid rgba(255, 255, 255, 0.1);
            margin-left: clamp(5px, 2vw, 10px); white-space: nowrap; flex-shrink: 0;
        }
        .auth-name-link {
            color: #94a3b8 !important; font-size: clamp(8px, 1.8vw, 10px) !important;
            font-weight: 700; letter-spacing: 1px; margin-right: clamp(6px, 1.5vw, 12px);
            display: flex; align-items: center; gap: 4px; white-space: nowrap;
        }
        .auth-name-link i { font-size: clamp(11px, 2vw, 13px); }
        .auth-name-link:hover { color: var(--text-light) !important; }
        
        /* Modified Auth Btn to be more compact */
        .auth-btn {
            background-color: var(--accent-copper);
            padding: clamp(5px, 1.2vw, 7px) clamp(12px, 3vw, 18px) !important;
            border-radius: 25px; color: white !important; font-weight: 700;
            font-size: clamp(8px, 1.8vw, 10px) !important; letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4);
            transition: all 0.3s ease; display: inline-flex; align-items: center;
            gap: clamp(4px, 1.5vw, 8px); white-space: nowrap;
        }
        .auth-btn i { font-size: clamp(10px, 2vw, 12px); }
        .auth-btn:hover { background-color: #4338ca; transform: translateY(-2px); }
        
        /* New Logout Button Style */
        .logout-btn {
            background-color: transparent !important;
            box-shadow: none !important;
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-left: 6px;
        }
        .logout-btn:hover, .logout-btn:active {
            background-color: #e11d48 !important; 
            border-color: #e11d48 !important;
            box-shadow: 0 4px 15px rgba(225, 29, 72, 0.4) !important;
            transform: translateY(-2px);
        }

        header.sticky .auth-wrapper { background: rgba(0, 0, 0, 0.2); border-color: rgba(255, 255, 255, 0.05); }
        header.sticky .logout-btn { border-color: rgba(255, 255, 255, 0.1); }

        /* --- HERO SECTION --- */
        .hero {
            height: 100vh; height: 100dvh; min-height: 650px;
            background: linear-gradient(rgba(15, 23, 42, 0.65), rgba(15, 23, 42, 0.85)), url('https://images.unsplash.com/photo-1593032465175-481ac7f401a0?auto=format&fit=crop&q=80&w=1920');
            background-size: cover; background-position: center top;
            display: flex; justify-content: center; align-items: center; text-align: center;
            color: var(--text-light); padding-top: clamp(60px, 12vh, 80px);
        }
        .hero-white-logo {
            width: clamp(80px, 16vw, 120px);
            height: auto;
            margin: 0 auto clamp(20px, 5vw, 35px);
            display: block;
            filter: grayscale(100%) invert(100%) brightness(200%) contrast(1000%); 
            mix-blend-mode: screen; 
            opacity: 0.95;
        }

        /* --- INFO & CONTACT CARDS SECTION --- */
        .info-cards-section { 
            background-color: #ffffff; 
            transform: translateY(clamp(-40px, -8vw, -80px)); 
            margin-bottom: clamp(-20px, -4vw, -40px); 
            position: relative; z-index: 10; 
        }
        .info-cards-grid { 
            display: flex; flex-direction: column; 
            box-shadow: 0 clamp(10px, 3vw, 15px) clamp(30px, 8vw, 50px) rgba(0,0,0,0.08); 
            border-radius: 12px; overflow: hidden; background: #fff;
        }
        .hours-card { 
            background-color: #1e293b; color: var(--text-light); 
            padding: clamp(60px, 10vw, 90px) clamp(20px, 5vw, 60px); 
            text-align: center;
        }
        .hours-card h3 { 
            font-size: clamp(14px, 4vw, 16px); letter-spacing: clamp(2px, 0.6vw, 3px); 
            margin-bottom: clamp(15px, 4vw, 25px); font-weight: 600; color: #ffffff; 
        }
        .hours-card p { 
            color: #94a3b8; font-size: clamp(12px, 3vw, 14px); 
            margin: 0 auto clamp(25px, 6vw, 40px); max-width: 500px; 
        }
        .hours-list { 
            display: flex; flex-direction: column; gap: clamp(12px, 3vw, 16px); 
            max-width: 500px; margin: 0 auto;
        }
        .hours-row { 
            display: flex; justify-content: space-between; font-size: clamp(12px, 3vw, 14px); 
            border-bottom: 1px solid #334155; padding-bottom: clamp(8px, 2vw, 12px); 
        }
        .hours-row span:last-child { color: var(--text-light); font-weight: 600; }
        
        .contact-bottom-bar { 
            display: flex; flex-direction: column; justify-content: center; align-items: center; 
            background-color: #f8fafc; 
            padding: clamp(30px, 6vw, 45px) 20px; 
        }
        .contact-title { 
            font-size: 11px; color: #64748b; margin-bottom: 20px; 
            font-weight: 700; text-transform: uppercase; letter-spacing: 2px; text-align: center; 
        }
        .contact-flex { 
            display: flex; justify-content: center; align-items: center; 
            gap: clamp(30px, 8vw, 80px); flex-wrap: wrap; 
        }
        .contact-item { 
            display: flex; align-items: center; gap: clamp(10px, 2vw, 15px); 
            font-size: clamp(14px, 3.5vw, 18px); font-weight: 700; color: #0f172a; 
            transition: opacity 0.3s;
        }
        .contact-item:hover { opacity: 0.7; }
        .contact-item i { font-size: clamp(20px, 5vw, 26px); }
        .contact-item .fa-phone-flip { color: #0f172a; }
        .contact-item .fa-whatsapp { color: #25d366; }

        /* --- QUALITY SECTION --- */
        .quality-section { padding: clamp(30px, 8vw, 60px) 0 clamp(40px, 12vw, 90px) 0; text-align: center; }
        .quality-section .tagline { font-family: var(--font-serif); font-style: italic; color: var(--accent-copper); font-size: clamp(13px, 3.5vw, 16px); margin-bottom: clamp(3px, 1vw, 5px); }
        .quality-section h2 { font-size: clamp(18px, 6vw, 26px); font-weight: 600; letter-spacing: clamp(2px, 1vw, 4px); text-transform: uppercase; margin-bottom: clamp(25px, 6vw, 50px); color: #0f172a; }
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(clamp(140px, 42vw, 280px), 1fr)); gap: clamp(10px, 3vw, 20px); }
        .gallery-item { position: relative; overflow: hidden; aspect-ratio: 3/4; background-color: #eee; border-radius: clamp(6px, 1.5vw, 8px); }
        .gallery-item img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s ease; filter: grayscale(0.8); }
        .gallery-item:hover img { transform: scale(1.06); filter: grayscale(0); }

        /* --- SERVICES SECTION --- */
        .services-section { background-color: #ffffff; padding: clamp(50px, 10vw, 80px) 0; }
        .services-header { text-align: center; font-size: clamp(24px, 5vw, 32px); font-weight: 700; color: #0f172a; margin-bottom: clamp(30px, 6vw, 50px); }
        .services-grid { display: grid; grid-template-columns: 1fr; gap: 30px; text-align: center; }
        @media(min-width: 576px) { .services-grid { grid-template-columns: repeat(2, 1fr); } }
        @media(min-width: 992px) { .services-grid { grid-template-columns: repeat(4, 1fr); } }
        
        .service-card .img-box { 
            width: clamp(120px, 15vw, 150px); height: clamp(120px, 15vw, 150px); 
            margin: 0 auto 20px; border-radius: 50%; overflow: hidden; 
            border: 5px solid #f0f9ff; box-shadow: 0 10px 20px rgba(79, 70, 229, 0.1); 
        }
        .service-card img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
        .service-card:hover img { transform: scale(1.1); }
        .service-card h4 { font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 10px; }
        .service-card p { font-size: 12px; color: #64748b; line-height: 1.6; }

        /* --- DISCOVER SECTION --- */
        .discover-section { background-color: #f8fafc; padding: clamp(50px, 10vw, 80px) 0; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; }
        .two-col-grid { display: grid; grid-template-columns: 1fr; gap: clamp(40px, 8vw, 60px); align-items: center; }
        @media(min-width: 992px) { .two-col-grid { grid-template-columns: 1fr 1fr; } }
        
        .img-overlap-group { position: relative; width: 100%; max-width: 450px; margin: 0 auto; aspect-ratio: 1; }
        .img-overlap-group .img-back { position: absolute; top: 0; right: 0; width: 75%; height: 75%; object-fit: cover; border-radius: 12px; box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .img-overlap-group .img-front { position: absolute; bottom: 0; left: 0; width: 65%; height: 65%; object-fit: cover; border-radius: 12px; border: 8px solid #f8fafc; box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        
        .content-block .subtitle { color: var(--accent-copper); font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 5px; }
        .content-block h2 { font-size: clamp(24px, 5vw, 36px); font-weight: 700; color: #0f172a; margin-bottom: 20px; line-height: 1.2; }
        .content-block p { color: #64748b; font-size: 14px; margin-bottom: 30px; line-height: 1.7; }
        
        .stats-flex { display: flex; gap: clamp(20px, 5vw, 40px); margin-bottom: 30px; }
        .stats-flex h3 { font-size: clamp(24px, 5vw, 36px); font-weight: 700; color: #0f172a; }
        .stats-flex p { color: #64748b; font-size: 13px; font-weight: 500; }

        /* --- WHY CHOOSE US SECTION --- */
        .why-section { background-color: #ffffff; padding: clamp(50px, 10vw, 80px) 0; }
        .features-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .feature-box { background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; transition: transform 0.3s; }
        .feature-box:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(79, 70, 229, 0.1); border-color: #c7d2fe; }
        .feature-box i { color: var(--accent-copper); font-size: 24px; margin-bottom: 12px; }
        .feature-box h4 { font-size: 14px; font-weight: 700; color: #0f172a; margin-bottom: 6px; }
        .feature-box p { font-size: 12px; color: #64748b; line-height: 1.5; }

        /* --- RATINGS & REVIEWS STYLING --- */
        .ratings-section { background-color: #ffffff; color: #1e293b; padding: clamp(50px, 10vw, 80px) 0; border-top: 1px solid #f1f5f9; }
        .ratings-container { max-width: 850px; width: 100%; margin: 0 auto; padding: 0 20px; }
        .ratings-header { font-size: clamp(22px, 5vw, 26px); font-weight: 700; margin-bottom: 40px; color: #0f172a; text-align: center; text-transform: uppercase; letter-spacing: 2px; }
        .ratings-flex { display: flex; align-items: center; justify-content: center; gap: clamp(40px, 8vw, 80px); margin-bottom: 50px; border-bottom: 1px solid #f1f5f9; padding-bottom: 30px; }
        .ratings-left { display: flex; flex-direction: column; align-items: center; justify-content: center; border-right: 1px solid #e2e8f0; padding-right: clamp(30px, 6vw, 50px); }
        .ratings-score { font-size: clamp(65px, 15vw, 88px); font-weight: 700; line-height: 1; margin-bottom: 8px; color: #0f172a; letter-spacing: -2px; }
        .ratings-count { font-size: 12px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; white-space: nowrap; }
        .ratings-right { flex: 1; display: flex; flex-direction: column; gap: 12px; width: 100%; max-width: 420px; }
        .rating-bar-row { display: flex; align-items: center; gap: 12px; }
        .rating-star-num { font-size: 13px; color: #334155; font-weight: 600; width: 10px; text-align: right; }
        .rating-star-icon { color: #ff9800; font-size: 13px; }
        .rating-track { flex: 1; height: 12px; background-color: #f1f5f9; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; }
        .rating-fill { height: 100%; background-color: #ff9800; border-radius: 12px; transition: width 1s ease-out; }
        
        .public-reviews-heading { font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 25px; letter-spacing: 1px; text-transform: uppercase; border-left: 4px solid var(--accent-copper); padding-left: 10px; }
        .public-reviews-list { display: grid; grid-template-columns: 1fr; gap: 24px; }
        @media(min-width: 768px) { .public-reviews-list { grid-template-columns: 1fr 1fr; } }
        
        .public-review-card { background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04); transition: all 0.3s ease; display: flex; flex-direction: column; }
        .public-review-card:hover { transform: translateY(-4px); box-shadow: 0 12px 25px rgba(0, 0, 0, 0.08); border-color: #c7d2fe; }
        .public-review-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; }
        .public-review-user-info { display: flex; flex-direction: column; }
        .public-review-user { font-weight: 700; color: #0f172a; font-size: 15px; }
        .public-review-garment { font-size: 11px; color: var(--accent-copper); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px; }
        .public-review-stars { color: #fbbf24; font-size: 12px; display: flex; gap: 2px; }
        .public-review-text { font-size: 14px; color: #475569; font-style: normal; line-height: 1.6; position: relative; flex-grow: 1; }
        .public-review-text::before { content: '\201C'; font-size: 40px; color: #e2e8f0; font-family: var(--font-serif); position: absolute; top: -15px; left: -10px; z-index: 0; line-height: 1; opacity: 0.5; }
        .public-review-text span { position: relative; z-index: 1; }

        /* --- FOOTER --- */
        footer { background-color: #0f172a; color: var(--text-light); padding: clamp(40px, 10vw, 80px) 0 clamp(20px, 5vw, 30px) 0; }
        .footer-grid { display: grid; grid-template-columns: 1fr; gap: clamp(25px, 6vw, 50px); border-bottom: 1px solid #1e293b; padding-bottom: clamp(30px, 8vw, 60px); margin-bottom: clamp(18px, 4vw, 30px); }
        .footer-col h3 { font-size: clamp(11px, 2.8vw, 13px); letter-spacing: clamp(2px, 0.6vw, 3px); margin-bottom: clamp(18px, 4vw, 25px); text-transform: uppercase; font-weight: 700; color: var(--accent-copper); }
        .footer-col.brand-col h2 { font-family: var(--font-serif); font-size: clamp(18px, 4.5vw, 22px); letter-spacing: 1px; margin-bottom: clamp(10px, 2.5vw, 15px); color: #ffffff; }
        .footer-col.brand-col span { color: var(--accent-copper); }
        .footer-col.brand-col p { font-size: clamp(11px, 2.8vw, 13px); color: #94a3b8; margin-bottom: clamp(20px, 5vw, 30px); max-width: 280px; }
        
        .bottom-bar { display: flex; flex-direction: column; gap: clamp(10px, 3vw, 15px); align-items: center; font-size: clamp(10px, 2.2vw, 11px); color: #94a3b8; letter-spacing: 1px; }
        @media (min-width: 480px) { .bottom-bar { flex-direction: row; justify-content: space-between; } }
        .scroll-top { width: clamp(30px, 7vw, 35px); height: clamp(30px, 7vw, 35px); border: 1px solid #334155; display: flex; justify-content: center; align-items: center; cursor: pointer; transition: all 0.3s; border-radius: 4px; }
        .scroll-top:hover { background-color: var(--accent-copper); border-color: var(--accent-copper); color: var(--text-light); }

        @media (min-width: 1440px) {
            .container { max-width: 1300px; }
            .gallery-grid { grid-template-columns: repeat(4, 1fr); gap: 24px; }
        }
        @media (max-width: 1150px) {
            .desktop-nav { gap: clamp(8px, 2vw, 12px); }
            .auth-wrapper { padding: 2px 2px 2px 6px; margin-left: 4px; }
            .auth-name-link { margin-right: 6px; }
        }
        @media (max-width: 767px) {
            .menu-toggle { display: flex; }
            .desktop-nav {
                position: fixed; top: 0; right: -100%; width: 80%; height: 100vh; height: 100dvh;
                background-color: rgba(15, 23, 42, 0.98);
                flex-direction: column; justify-content: center; gap: clamp(25px, 6vw, 35px);
                transition: right 0.4s ease; box-shadow: -10px 0 30px rgba(0,0,0,0.5);
                backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
                padding: clamp(40px, 10vh, 60px) clamp(20px, 5vw, 30px);
            }
            .desktop-nav.active { right: 0; }
            .auth-wrapper {
                flex-direction: column; background: transparent; border: none;
                gap: clamp(12px, 3vw, 15px); padding: 0; margin-left: 0;
                margin-top: clamp(15px, 4vw, 20px); width: 100%;
            }
            .gallery-grid { grid-template-columns: repeat(2, 1fr); gap: clamp(8px, 2vw, 12px); }
        }
        @media (max-width: 600px) {
            .ratings-flex { flex-direction: column; gap: 25px; }
            .ratings-left { border-right: none; padding-right: 0; border-bottom: 1px solid #f1f5f9; padding-bottom: 25px; width: 100%; }
        }
        @media (max-width: 576px) {
            .gallery-grid { grid-template-columns: 1fr 1fr; gap: clamp(6px, 2vw, 10px); }
        }
        @media (max-width: 380px) {
            .gallery-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <header id="main-header">
        <div class="container nav-container">
            <div class="logo-wrapper">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Dream Tailors Logo" onerror="this.src='https://ui-avatars.com/api/?name=DT&background=4f46e5&color=fff'" loading="eager" width="40" height="40">
                <div class="logo"><?php echo $header_logo_text; ?></div>
            </div>
            <div class="menu-toggle" id="mobile-toggle" aria-label="Toggle menu">
                <span></span><span></span><span></span>
            </div>
            
            <ul class="desktop-nav" id="nav-list">
                <li class="active"><a href="#home">HOME</a></li>
                <li><a href="#about">ABOUT US</a></li>
                <li><a href="#process">OUR PROCESS</a></li>
                <li><a href="#gallery">GALLERY</a></li>
                <li><a href="#reviews">REVIEWS</a></li>
                
                <li class="auth-wrapper">
                    @if(Auth::guard('customer')->check())
                        <a href="{{ route('customer.dashboard') }}" class="auth-name-link">
                            <i class="fa-solid fa-circle-user" style="color: var(--accent-copper);"></i> WELCOME, {{ strtoupper(explode(' ', Auth::guard('customer')->user()->name)[0]) }}
                        </a>
                        <a href="{{ route('customer.dashboard') }}" class="auth-btn">
                            <i class="fa-solid fa-layer-group"></i> DASHBOARD
                        </a>
                        <!-- Updated Logout Button -->
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="auth-btn logout-btn">
                            <i class="fa-solid fa-power-off"></i> LOG OUT
                        </a>
                    
                    @elseif(Auth::check())
                        <a href="{{ route('dashboard') }}" class="auth-name-link" style="color: #4f46e5 !important;">
                            <i class="fa-solid fa-user-shield"></i> WELCOME, ADMIN
                        </a>
                        <a href="{{ route('dashboard') }}" class="auth-btn">
                            <i class="fa-solid fa-chart-line"></i> ADMIN PANEL
                        </a>
                        <!-- Updated Logout Button -->
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="auth-btn logout-btn">
                            <i class="fa-solid fa-power-off"></i> LOG OUT
                        </a>
                    
                    @else
                        <span class="auth-name-link" style="cursor: default;">
                            <i class="fa-regular fa-user"></i> WELCOME, GUEST
                        </span>
                        <a href="{{ route('login') }}" class="auth-btn">
                            <i class="fa-solid fa-arrow-right-to-bracket"></i> SIGN IN
                        </a>
                    @endif

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    </header>

    <section id="home" class="hero">
        <div class="hero-content">
            <img src="{{ asset('images/logo.jpeg') }}" alt="Dream Tailors Logo" class="hero-white-logo" onerror="this.style.display='none'">
            
            <p style="max-width: 720px; margin: 0 auto; line-height: 1.8; font-family: var(--font-sans); font-weight: 300; font-size: clamp(14px, 3.5vw, 17px); color: #ffffff; letter-spacing: 0.5px;">
                Dream Tailors specializes in premium bespoke tailoring, blending classic craftsmanship with modern style.
                We craft custom garments designed to fit your unique personality and measurements.
                Ensuring elegance in every stitch, we focus on durability, comfort, and a perfect fit.
                Experience the balance of timeless design and precision tailoring with our expert services.
            </p>
        </div>
    </section>

    <section id="about" class="info-cards-section reveal-section">
        <div class="container">
            <div class="info-cards-grid">
                <div class="hours-card">
                    <h3 style="color: #ffffff;"><?php echo $hours_title; ?></h3>
                    <p><?php echo $hours_desc; ?></p>
                    <div class="hours-list">
                        <?php foreach($hours_days as $day => $time): ?>
                        <div class="hours-row"><span style="color: #ffffff;"><?php echo $day; ?></span><span style="color: #ffffff; font-weight: 600;"><?php echo $time; ?></span></div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="contact-bottom-bar">
                    <p class="contact-title">Direct Contact & Support</p>
                    <div class="contact-flex">
                        <a href="tel:<?php echo str_replace(' ', '', $contact_phone); ?>" class="contact-item">
                            <i class="fa-solid fa-phone-flip"></i>
                            <span><?php echo $contact_phone; ?></span>
                        </a>
                        <a href="https://wa.me/94<?php echo substr(str_replace(' ', '', $contact_whatsapp), 1); ?>" target="_blank" class="contact-item">
                            <i class="fa-brands fa-whatsapp"></i>
                            <span><?php echo $contact_whatsapp; ?></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="gallery" class="quality-section reveal-section">
        <div class="container">
            <p class="tagline"><?php echo $quality_tagline; ?></p>
            <h2><?php echo $quality_title; ?></h2>
            <div class="gallery-grid" id="galleryGrid">
                <?php foreach($gallery_items as $index => $item): ?>
                <div class="gallery-item" style="transition-delay: <?php echo $index * 0.08; ?>s"><img src="<?php echo $item[0]; ?>" alt="<?php echo $item[1]; ?>" loading="lazy" width="300" height="400"></div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section id="process" class="services-section reveal-section">
        <div class="container">
            <h2 class="services-header">Our Services</h2>
            <div class="services-grid">
                <div class="service-card">
                    <div class="img-box">
                        <img src="https://images.unsplash.com/photo-1612423284934-2850a4ea6b0f?auto=format&fit=crop&q=80&w=400" alt="Stylish Clothing">
                    </div>
                    <h4>Stylish Clothing</h4>
                    <p>Experience premium styling with our custom-made everyday wear and professional outfits.</p>
                </div>
                <div class="service-card">
                    <div class="img-box">
                        <img src="https://images.unsplash.com/photo-1594938298603-c8148c4dae35?auto=format&fit=crop&q=80&w=400" alt="Suits & Shirts">
                    </div>
                    <h4>Suits & Shirts</h4>
                    <p>Perfectly tailored blazers, trousers, and dress shirts stitched to your unique measurements.</p>
                </div>
                <div class="service-card">
                    <div class="img-box">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQYxPHAUA7JPDtdQR6gNHtlUPcUxBQ0jcq0pAoQ44qGmA&s=10" alt="Wedding Dresses">
                    </div>
                    <h4>Wedding Wear</h4>
                    <p>Make your special day memorable with our meticulously crafted wedding and bridal garments.</p>
                </div>
                <div class="service-card">
                    <div class="img-box">
                        <img src="https://images.unsplash.com/photo-1598808503746-f34c53b9323e?auto=format&fit=crop&q=80&w=400" alt="Custom Work">
                    </div>
                    <h4>Custom Work</h4>
                    <p>Bring your imagination to life. We handle bespoke patterns and custom fabric styling.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="discover-section reveal-section">
        <div class="container two-col-grid">
            <div class="img-overlap-group">
                <img src="https://m.media-amazon.com/images/I/61dKoGE6jAL._AC_SL1001_.jpg" class="img-back" alt="Suit Detail">
                <img src="https://img4.dhresource.com/webp/m/0x0/f3/albu/bw/l/13/02626bd8-b8c4-4863-9b70-19a2afe19b6f.jpg" class="img-front" alt="Tailor Working">
            </div>
            <div class="content-block">
                <span class="subtitle">Welcome</span>
                <h2>Discover True Quality</h2>
                <p>At Dream Tailors, we blend decades of experience with modern styling to deliver garments that truly reflect your personality. Every stitch is placed with precision, ensuring a perfect fit and long-lasting quality for every client.</p>
                <div class="stats-flex">
                    <div>
                        <h3>250+</h3>
                        <p>Suit Projects</p>
                    </div>
                    <div>
                        <h3>1250+</h3>
                        <p>Happy Clients</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="why-section reveal-section">
        <div class="container two-col-grid">
            <div class="content-block" style="order: 2;">
                <h2>Why Choose Us</h2>
                <p>We believe in excellence. From selecting the finest raw materials to the final fitting, our commitment to quality guarantees a garment you will love wearing.</p>
                
                <div class="features-grid">
                    <div class="feature-box">
                        <i class="fa-solid fa-gem"></i>
                        <h4>Good Fabric</h4>
                        <p>We source premium materials to ensure durability and comfort.</p>
                    </div>
                    <div class="feature-box">
                        <i class="fa-solid fa-ruler-combined"></i>
                        <h4>Best Fitting</h4>
                        <p>Accurate digital profiles ensure precise, body-hugging fits.</p>
                    </div>
                    <div class="feature-box">
                        <i class="fa-solid fa-handshake"></i>
                        <h4>Trusted Work</h4>
                        <p>Decades of experience delivering reliable bespoke tailoring.</p>
                    </div>
                    <div class="feature-box">
                        <i class="fa-solid fa-pen-nib"></i>
                        <h4>Unique Design</h4>
                        <p>Custom patterns created specifically for your personal style.</p>
                    </div>
                </div>
            </div>
            <div class="img-overlap-group" style="order: 1;">
                <img src="https://d1fufvy4xao6k9.cloudfront.net/images/blog/posts/2019/02/rawpixel_760104_unsplash_6339.jpg" class="img-back" alt="Elegant Man">
                <img src="https://blog.lanieri.com/wp-content/uploads/2020/03/forbici-e-metro-sartoriale-1170x550.jpg" class="img-front" alt="Measuring Process">
            </div>
        </div>
    </section>

    @php
        $feedbacks = \Illuminate\Support\Facades\DB::table('feedbacks')
            ->join('orders', 'feedbacks.order_id', '=', 'orders.id')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->select('feedbacks.*', 'customers.name as customer_name', 'orders.garment_name')
            ->where('feedbacks.is_read', 1) 
            ->get();
            
        $totalFeedbacks = $feedbacks->count();
        $averageRating = $totalFeedbacks > 0 ? round($feedbacks->avg('rating'), 1) : 0;
        
        $ratingCounts = [
            5 => $feedbacks->where('rating', 5)->count(),
            4 => $feedbacks->where('rating', 4)->count(),
            3 => $feedbacks->where('rating', 3)->count(),
            2 => $feedbacks->where('rating', 2)->count(),
            1 => $feedbacks->where('rating', 1)->count(),
        ];

        $randomThoughts = $totalFeedbacks > 5 ? $feedbacks->random(5) : $feedbacks;
    @endphp
    
    <section id="reviews" class="ratings-section reveal-section">
        <div class="container ratings-container">
            <h2 class="ratings-header">Ratings and reviews</h2>
            <div class="ratings-flex">
                <div class="ratings-left">
                    <div class="ratings-score">{{ number_format($averageRating, 1) }}</div>
                    <div class="ratings-count">{{ $totalFeedbacks }} RATINGS</div>
                </div>
                <div class="ratings-right">
                    @foreach([5, 4, 3, 2, 1] as $star)
                        @php 
                            $count = $ratingCounts[$star] ?? 0;
                            $percentage = $totalFeedbacks > 0 ? ($count / $totalFeedbacks) * 100 : 0;
                        @endphp
                        <div class="rating-bar-row">
                            <span class="rating-star-num">{{ $star }}</span>
                            <i class="fa-solid fa-star rating-star-icon"></i>
                            <div class="rating-track">
                                <div class="rating-fill" style="width: {{ $percentage }}%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @if($randomThoughts->count() > 0)
                <h3 class="public-reviews-heading">Clients Thoughts</h3>
                <div class="public-reviews-list">
                    @foreach($randomThoughts as $feedback)
                        <div class="public-review-card">
                            <div class="public-review-header">
                                <div class="public-review-user-info">
                                    <div class="public-review-user">{{ $feedback->customer_name }}</div>
                                    <div class="public-review-garment">{{ $feedback->garment_name }}</div>
                                </div>
                                <div class="public-review-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fa-{{ $i <= $feedback->rating ? 'solid' : 'regular' }} fa-star"></i>
                                    @endfor
                                </div>
                            </div>
                            @if($feedback->message)
                                <p class="public-review-text"><span>{{ $feedback->message }}</span></p>
                            @else
                                <p class="public-review-text"><span class="text-slate-400">Perfect fit. No message left.</span></p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <footer class="reveal-section">
        <div class="container footer-grid" style="grid-template-columns: 1fr; justify-items: left;">
            <div class="footer-col brand-col">
                <h2><?php echo $footer_brand_logo; ?></h2>
                <p><?php echo $footer_desc; ?></p>
                <h3 style="margin-top: 15px; margin-bottom: 15px;">INFO</h3>
                <div style="display: flex; flex-direction: column; gap: 12px; font-size: clamp(11px, 2.8vw, 13px); color: #94a3b8;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fa-solid fa-phone" style="color: var(--accent-copper); font-size: 14px;"></i> <?php echo $contact_phone_number; ?>
                    </div>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fa-solid fa-envelope" style="color: var(--accent-copper); font-size: 14px;"></i> <?php echo $contact_email; ?>
                    </div>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fa-solid fa-location-dot" style="color: var(--accent-copper); font-size: 14px;"></i> <?php echo $contact_address; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="container bottom-bar">
            <span><?php echo $copyright_text; ?></span>
            <div class="scroll-top" id="scrollTopBtn" aria-label="Scroll to top"><i class="fa-solid fa-chevron-up"></i></div>
        </div>
    </footer>

    <script>
        function revealSectionsOnScroll() {
            var sections = document.querySelectorAll('.reveal-section');
            var galleryItems = document.querySelectorAll('.gallery-item');
            function checkReveal() {
                var wh = window.innerHeight;
                var trigger = wh * 0.85;
                sections.forEach(function(s) {
                    if (s.getBoundingClientRect().top < trigger) s.classList.add('visible');
                });
                galleryItems.forEach(function(item) {
                    if (item.getBoundingClientRect().top < trigger) item.classList.add('visible');
                });
            }
            window.addEventListener('scroll', checkReveal, { passive: true });
            checkReveal();
        }
        revealSectionsOnScroll();

        window.addEventListener('scroll', function() {
            document.getElementById('main-header').classList.toggle('sticky', window.scrollY > 50);
        }, { passive: true });
        
        var mobileToggle = document.getElementById('mobile-toggle');
        var navList = document.getElementById('nav-list');
        mobileToggle.addEventListener('click', function() {
            this.classList.toggle('active');
            navList.classList.toggle('active');
            document.body.style.overflow = navList.classList.contains('active') ? 'hidden' : '';
        });
        
        document.querySelectorAll('.desktop-nav a').forEach(function(link) {
            link.addEventListener('click', function() {
                mobileToggle.classList.remove('active');
                navList.classList.remove('active');
                document.body.style.overflow = '';
            });
        });
        
        document.getElementById('scrollTopBtn').addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
</body>
</html>