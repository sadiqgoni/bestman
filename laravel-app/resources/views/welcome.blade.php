<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestman Merchandise | Reliable Petroleum Supply</title>
    <style>
        :root { --blue: #083b91; --deep: #06265f; --red: #d5222a; --ink: #11213d; --muted: #5f6d82; --line: #dfe6f0; --soft: #f4f7fb; }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { margin: 0; color: var(--ink); background: #fff; font-family: Georgia, 'Times New Roman', serif; }
        a { color: inherit; text-decoration: none; }
        .header { position: sticky; top: 0; z-index: 5; background: rgba(255,255,255,.97); border-bottom: 1px solid var(--line); }
        .nav { max-width: 1180px; min-height: 78px; margin: auto; padding: 12px 24px; display: flex; align-items: center; gap: 26px; }
        .logo { display: block; width: 205px; height: auto; }
        .links { display: flex; gap: 22px; align-items: center; margin-left: auto; color: #34445e; font: 600 14px Arial, sans-serif; }
        .links a:hover { color: var(--red); }
        .staff-button, .hero-button, .contact-button { display: inline-flex; align-items: center; justify-content: center; border-radius: 4px; padding: 12px 18px; font: 700 13px Arial, sans-serif; }
        .staff-button { color: #fff; background: var(--red); white-space: nowrap; }
        .staff-button:hover { background: #af171f; }
        .hero { min-height: 570px; display: flex; align-items: center; color: #fff; background: linear-gradient(112deg, rgba(4,35,91,.97), rgba(8,59,145,.84)), url('/images/bestman-logo.svg') right 8% center / 520px auto no-repeat; }
        .hero-inner { width: min(1180px, 100%); margin: auto; padding: 72px 24px 96px; }
        .eyebrow, .kicker { color: #f2c54e; font: 700 12px Arial, sans-serif; letter-spacing: .18em; text-transform: uppercase; }
        h1 { max-width: 680px; margin: 16px 0; font-size: clamp(42px, 6vw, 76px); line-height: .98; letter-spacing: -.03em; }
        .hero p { max-width: 650px; color: #dbe8ff; font-size: 20px; line-height: 1.55; }
        .hero-button { margin-top: 18px; color: var(--blue); background: #fff; }
        .hero-button:hover { background: #f2c54e; }
        .section { max-width: 1180px; margin: auto; padding: 88px 24px; }
        .kicker { color: var(--red); letter-spacing: .16em; }
        h2 { margin: 10px 0 18px; color: var(--deep); font-size: clamp(30px, 4vw, 48px); line-height: 1.08; }
        .intro { max-width: 700px; color: var(--muted); font-size: 18px; line-height: 1.65; }
        .products { background: var(--soft); }
        .product-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 34px; }
        .product { padding: 30px; background: #fff; border-top: 5px solid var(--red); box-shadow: 0 12px 30px rgba(14,39,80,.08); }
        .product h3 { margin: 0 0 12px; color: var(--blue); font-size: 27px; }
        .product p, .about p, .commitment p { margin: 0; color: var(--muted); font: 16px/1.7 Arial, sans-serif; }
        .about { display: grid; grid-template-columns: .85fr 1.15fr; gap: 70px; align-items: start; }
        .commitments { display: grid; gap: 18px; margin-top: 24px; }
        .commitment { padding: 20px 0 20px 22px; border-left: 3px solid var(--blue); }
        .commitment h3 { margin: 0 0 7px; color: var(--deep); font-size: 21px; }
        .commitment p { font-size: 15px; }
        .contact-band { color: #fff; background: var(--red); }
        .contact-band .section { display: flex; align-items: center; justify-content: space-between; gap: 24px; padding-top: 44px; padding-bottom: 44px; }
        .contact-band h2 { margin: 0; color: #fff; font-size: 32px; }
        .contact-band p { margin: 8px 0 0; font: 15px Arial, sans-serif; }
        .contact-button { border: 1px solid #fff; white-space: nowrap; }
        footer { color: #dbe8ff; background: var(--deep); }
        .footer-grid { max-width: 1180px; margin: auto; padding: 42px 24px 28px; display: grid; grid-template-columns: 1.5fr 1fr 1fr; gap: 32px; }
        footer h3 { margin: 0 0 14px; color: #fff; font-size: 18px; }
        footer p, footer a { font: 14px/1.7 Arial, sans-serif; }
        footer a:hover { color: #f2c54e; }
        .copyright { max-width: 1180px; margin: auto; padding: 18px 24px; border-top: 1px solid rgba(255,255,255,.15); color: #a9bbd8; font: 12px Arial, sans-serif; }
        @media (max-width: 850px) { .nav { flex-wrap: wrap; gap: 14px; } .links { order: 3; width: 100%; overflow-x: auto; justify-content: flex-start; margin-left: 0; padding-bottom: 4px; } .hero { background-position: center; } .product-grid, .about, .footer-grid { grid-template-columns: 1fr; } .about { gap: 35px; } .contact-band .section { align-items: flex-start; flex-direction: column; } }
        @media (max-width: 480px) { .nav { padding: 12px 16px; } .logo { width: 175px; } .staff-button { margin-left: auto; padding: 10px 12px; } .section, .hero-inner, .footer-grid { padding-left: 18px; padding-right: 18px; } h1 { font-size: 43px; } .hero p { font-size: 17px; } }
    </style>
</head>
<body>
<header class="header"><nav class="nav" aria-label="Main navigation">
    <a href="#home" aria-label="Bestman Merchandise home"><img class="logo" src="{{ asset('images/bestman-logo.svg') }}" alt="Bestman Merchandise Nig. Ltd."></a>
    <div class="links"><a href="#home">Home</a><a href="#about">About Us</a><a href="#products">Our Products</a><a href="#locations">Station Locations</a><a href="#contact">Contact Us</a></div>
    <a class="staff-button" href="{{ url('/staff/login') }}">Staff Login</a>
</nav></header>
<main>
<section class="hero" id="home"><div class="hero-inner"><div class="eyebrow">Fuel supply you can measure</div><h1>Reliable Petroleum &amp; Energy Supply Across Nigeria</h1><p>Supplying premium Premium Motor Spirit (PMS), Automotive Gas Oil (Diesel), and Dual Purpose Kerosene (DPK) with integrity and accuracy.</p><a class="hero-button" href="#products">Explore Products</a></div></section>
<section class="section products" id="products"><div class="kicker">Our products</div><h2>Energy for every journey and operation.</h2><p class="intro">Dependable petroleum products, handled with care from supply drop-off to the end user.</p><div class="product-grid"><article class="product"><h3>PMS</h3><p>Premium Motor Spirit for personal and commercial transportation, supplied with reliable quality and accurate dispensing.</p></article><article class="product"><h3>AGO / Diesel</h3><p>Dependable energy for heavy machinery, haulage fleets, industrial generators, and demanding operations.</p></article><article class="product"><h3>DPK</h3><p>Dual Purpose Kerosene for safe, clean-burning domestic and industrial applications.</p></article></div></section>
<section class="section about" id="about"><div><div class="kicker">About Bestman</div><h2>Trade built on accuracy, continuity, and trust.</h2></div><div class="about-copy"><p>Bestman Merchandise Nig Ltd, headquartered at 68 E Bello Rd, Fagge, Kano, is an established commercial enterprise committed to excellence in distributive trade and downstream supply operations.</p><p style="margin-top:18px">From calibrated metering to quality-controlled sourcing, our operations are designed for transparent dispensing, continuous supply chain performance, and dependable fuel distribution across Nigeria.</p><div class="commitments"><div class="commitment"><h3>Transparent dispensing</h3><p>Precision metering, regulatory awareness, and auditable product movement protect every customer.</p></div><div class="commitment"><h3>Continuous operations</h3><p>Strategic stock planning and monitored logistics help reduce disruption and keep supply moving.</p></div><div class="commitment"><h3>Quality distribution</h3><p>Verified sourcing and anti-contamination checks preserve product integrity throughout transit.</p></div></div></div></section>
<section class="contact-band" id="contact"><div class="section"><div><h2>Need dependable supply?</h2><p>Talk to Bestman Merchandise at our Kano headquarters.</p></div><a class="contact-button" href="mailto:info@bestman.local">Contact Us</a></div></section>
</main>
<footer id="locations"><div class="footer-grid"><div><img class="logo" src="{{ asset('images/bestman-logo.svg') }}" alt="Bestman Merchandise Nig. Ltd."><p>BESTMAN MERCHANDISE NIG. LTD.</p></div><div><h3>Quick Links</h3><p><a href="#products">Products</a><br><a href="#about">About Us</a><br><a href="#locations">Station Network</a></p></div><div><h3>Employee Access</h3><p><a href="{{ url('/staff/login') }}">Employee Portal / Staff Access</a></p><p>68 E Bello Rd, Fagge, Kano</p></div></div><div class="copyright">&copy; {{ date('Y') }} Bestman Merchandise Nig. Ltd. All rights reserved.</div></footer>
</body>
</html>
