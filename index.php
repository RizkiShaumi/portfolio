<?php
declare(strict_types=1);
// ponytail: contact stored in flat json log; upgrade to PHPMailer/SMTP or DB when mail service is ready.
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
function validate_contact(array $input, string $sessionToken): array {
    $token = $input['csrf_token'] ?? '';
    if (!hash_equals($sessionToken, (string)$token)) {
        return ['ok' => false, 'error' => 'Invalid security token. Please refresh and try again.'];
    }
    $name = trim((string)($input['name'] ?? ''));
    $email = trim((string)($input['email'] ?? ''));
    $message = trim((string)($input['message'] ?? ''));

    if (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
        return ['ok' => false, 'error' => 'Nama harus antara 2 hingga 100 karakter.'];
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['ok' => false, 'error' => 'Alamat email tidak valid.'];
    }
    if (mb_strlen($message) < 5 || mb_strlen($message) > 3000) {
        return ['ok' => false, 'error' => 'Pesan minimal 5 karakter.'];
    }
    return [
        'ok' => true,
        'entry' => [
            'timestamp' => date('c'),
            'name' => $name,
            'email' => $email,
            'message' => $message,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ]
    ];
}

$feedback = null;
$feedbackType = 'success';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $res = validate_contact($_POST, $_SESSION['csrf_token'] ?? '');
    if (!$res['ok']) {
        $feedback = $res['error'];
        $feedbackType = 'error';
    } else {
        $logFile = __DIR__ . DIRECTORY_SEPARATOR . 'contacts.json';
        $existing = file_exists($logFile) ? json_decode((string)file_get_contents($logFile), true) : [];
        if (!is_array($existing)) { $existing = []; }
        $existing[] = $res['entry'];
        file_put_contents($logFile, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $feedback = 'Terima kasih! Pesan Anda telah berhasil dikirim.';
        $feedbackType = 'success';
    }
}
$profile = [
    'name'     => 'Rizki Shaumi Sabiq',
    'tagline'  => 'Scalable Infrastructure. Automated Security.',
    'role'     => 'Cloud & DevOps Engineer / SysAdmin',
    'location' => 'Garut, Jawa Barat, Indonesia',
    'email'    => 'rizki.shaumi.dev@gmail.com',
    'phone'    => '+62 882-2653-9328',
    'linkedin' => 'https://www.linkedin.com/in/rizki-shaumi-sabiq',
    'github'   => 'https://github.com',
    'summary'  => 'Lulusan Sarjana Terapan Rekayasa Rekayasa Komputer dengan fokus mendalam pada Cloud Computing, Virtualisasi, Keamanan Siber, dan Otomasi Sistem. Terbiasa mengelola infrastruktur AWS, VMware vSphere, Proxmox, serta automasi sistem menggunakan Docker, Python, dan PHP.',
];
$tools = [
    'AWS Cloud', 'Docker', 'VMware vSphere', 'Proxmox VE', 'pfSense', 'Snort IDS/IPS',
    'Kali Linux', 'Ubuntu Server', 'Python', 'PHP', 'Bash Scripting', 'Nmap',
    'OWASP ZAP', 'MySQL / RDS', 'MikroTik', 'Git'
];
$projects = [
    [
        'title'       => 'IoT AC Monitoring & Usage Prediction (AWS)',
        'category'    => 'Cloud & IoT Architecture',
        'badge'       => 'Production Grade',
        'desc'        => 'Platform pemantauan AC berbasis cloud secara real-time dengan prediksi beban menggunakan Machine Learning. Arsitektur backend PHP & Flutter di EC2, VPC terisolasi, database Amazon RDS MySQL, dan cron serverless via AWS Lambda & EventBridge.',
        'tech'        => ['AWS EC2', 'AWS Lambda', 'Amazon RDS', 'PHP', 'MQTT', 'Python'],
        'link'        => '#',
    ],
    [
        'title'       => 'Malware Incident Handling & Root Cause Analysis',
        'category'    => 'Cyber Security & Incident Response',
        'badge'       => 'Security Case Study',
        'desc'        => 'Investigasi komprehensif insiden breach server produksi. Menemukan payload cryptomining yang menyamar sebagai layanan Immunify360, mengaudit celah SSRF pada API endpoint, dan memitigasi risiko dengan container isolation & network egress rules.',
        'tech'        => ['Incident Response', 'SSRF RCA', 'Docker Hardening', 'Linux Forensics', 'Network Egress'],
        'link'        => '#',
    ],
    [
        'title'       => 'Automated Web Vulnerability Scanner Interface',
        'category'    => 'SecOps & Automation Tooling',
        'badge'       => 'Security Tooling',
        'desc'        => 'Dashboard terpusat untuk otomatisasi asesmen keamanan website. Mengintegrasikan OWASP ZAP untuk DAST vulnerabilities, Nmap untuk enumerasi port/service, dan SSLyze untuk evaluasi kelemahan SSL/TLS.',
        'tech'        => ['OWASP ZAP', 'Nmap', 'SSLyze', 'Python Backend', 'Web Dashboard'],
        'link'        => '#',
    ],
    [
        'title'       => 'AI WhatsApp Assistant for Umrah Travel Services',
        'category'    => 'AI & Integration',
        'badge'       => 'Applied AI',
        'desc'        => 'Chatbot pintar WhatsApp untuk otomasi layanan tanya-jawab paket Umrah, ketersediaan jadwal penerbangan, dan integrasi API backend LLM untuk respons dinamis dan akurat.',
        'tech'        => ['LLM API', 'WhatsApp Business API', 'PHP Backend', 'JSON Webhook'],
        'link'        => '#',
    ],
];
$experiences = [
    [
        'role'    => 'Cyber Security & System Administrator (Internship)',
        'company' => 'Dinas Komunikasi Dan Informatika Garut',
        'period'  => 'Jul 2024 - Des 2024',
        'points'  => [
            'Deploy & konfigurasi virtual machine di lingkungan VMware vSphere untuk infrastruktur cloud instansi.',
            'Implementasi pfSense firewall, bridge interface, dan filtering traffic berbasis aturan.',
            'Kustomisasi rule Snort IDS/IPS guna mendeteksi pemindaian port (TCP SYN, FIN, XMAS, NULL, OS fingerprint).',
            'Simulasi penetration testing berkala dari Kali Linux menggunakan Nmap dan Metasploit.',
            'Setup VMware ESXi 8 di server fisik HPE dengan konfigurasi RAID dan optimasi cluster vCenter & vMotion.',
            'Manajemen penyimpanan terpusat VM menggunakan Windows Storage Server.'
        ]
    ]
];
$education = [
    [
        'institution' => 'Politeknik Negeri Semarang',
        'degree'      => 'Sarjana Terapan Teknik Telekomunikasi / Rekayasa Komputer',
        'period'      => '2021 - 2025',
        'meta'        => 'IPK: 3.64 / 4.00'
    ]
];
$certs = [
    [
        'title'  => 'AWS Certified Cloud Practitioner',
        'issuer' => 'Amazon Web Services (AWS)',
        'period' => '2026 - 2029',
        'desc'   => 'Validasi pemahaman menyeluruh atas arsitektur cloud AWS, keamanan, harga, dan layanan inti.'
    ],
    [
        'title'  => 'MikroTik Certified Network Associate (MTCNA)',
        'issuer' => 'MikroTik',
        'period' => '2025 - 2028',
        'desc'   => 'Konfigurasi routing, firewall filter, NAT, bandwidth management, dan monitoring jaringan.'
    ],
    [
        'title'  => 'AWS Re/Start Program Graduate',
        'issuer' => 'Amazon Web Services',
        'period' => '2025 - 2026',
        'desc'   => 'Pelatihan intensif 500+ jam mencakup Linux sysadmin, networking, scripting Python, dan AWS services.'
    ],
    [
        'title'  => 'TOEIC Official Score: 920',
        'issuer' => 'ETS Global',
        'period' => '2024 - 2026',
        'desc'   => 'Taraf kemahiran bahasa Inggris profesional tingkat lanjut (C1 Proficient).'
    ],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($profile['name']) ?> | Cloud & DevOps Portfolio</title>
    <meta name="description" content="Portofolio resmi <?= htmlspecialchars($profile['name']) ?> - Cloud, Virtualisasi, Cyber Security & DevOps.">
    <style>
        :root {
            --bg-main: #020b06;
            --bg-card: linear-gradient(180deg, #041d11 0%, #082918 100%);
            --bg-card-hover: linear-gradient(180deg, #072a19 0%, #0c3821 100%);
            --border-card: #143823;
            --border-glow: #22c55e44;
            --accent-green: #C9F7C3;
            --brand-green: #10b981;
            --text-main: #ecfdf5;
            --text-muted: #94a3b8;
            --font-stack: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        html {
            scroll-behavior: smooth;
        }
        body {
            font-family: var(--font-stack);
            background-color: var(--bg-main);
            color: var(--text-main);
            line-height: 1.6;
            background-image: 
                radial-gradient(rgba(34, 197, 94, 0.12) 1px, transparent 1px),
                radial-gradient(ellipse at 50% 0%, rgba(16, 185, 129, 0.15), transparent 70%);
            background-size: 28px 28px, 100% 900px;
            background-repeat: repeat, no-repeat;
            min-height: 100vh;
        }
        a {
            color: inherit;
            text-decoration: none;
        }
        .container {
            max-width: 1180px;
            margin: 0 auto;
            padding: 0 1.25rem;
        }
        /* Navbar */
        .navbar {
            position: fixed;
            top: 1.25rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 100;
            background: rgba(3, 24, 14, 0.82);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(201, 247, 195, 0.18);
            border-radius: 9999px;
            padding: 0.6rem 1.6rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.8rem;
            list-style: none;
        }
        .nav-links a {
            font-size: 0.92rem;
            font-weight: 500;
            color: #d1fae5;
            transition: color 0.2s ease;
        }
        .nav-links a:hover {
            color: var(--accent-green);
        }
        /* Hero */
        .hero {
            padding: 9rem 0 4rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
        }
        .avatar-wrap {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            padding: 5px;
            background: linear-gradient(135deg, #10b981, #052e16);
            box-shadow: 0 0 35px rgba(16, 185, 129, 0.35);
            margin-bottom: 1.5rem;
        }
        .avatar {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: #064e3b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.8rem;
            font-weight: 700;
            color: var(--accent-green);
            overflow: hidden;
        }
        .pill-badge {
            display: inline-block;
            font-size: 0.75rem;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--accent-green);
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(201, 247, 195, 0.25);
            padding: 0.35rem 1rem;
            border-radius: 9999px;
            margin-bottom: 1rem;
        }
        .hero h1 {
            font-size: clamp(2.2rem, 5vw, 3.8rem);
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 1rem;
            color: #ffffff;
        }
        .hero h1 .highlight {
            color: var(--accent-green);
            display: inline-block;
        }
        .hero p.lead {
            max-width: 720px;
            color: #a7f3d0;
            font-size: 1.12rem;
            margin-bottom: 2rem;
        }
        .btn-group {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.85rem 1.8rem;
            font-size: 0.95rem;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.25s ease;
            cursor: pointer;
            border: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.45);
        }
        .btn-outline {
            background: transparent;
            color: var(--accent-green);
            border: 1px solid var(--accent-green);
        }
        .btn-outline:hover {
            background: rgba(201, 247, 195, 0.1);
            transform: translateY(-2px);
        }
        /* Bento Grid */
        .bento-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 1.25rem;
            margin: 2rem 0 5rem;
        }
        .bento-card {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: 20px;
            padding: 1.75rem;
            transition: transform 0.25s ease, border-color 0.25s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .bento-card:hover {
            transform: translateY(-3px);
            border-color: var(--border-glow);
        }
        .bento-col-7 { grid-column: span 7; }
        .bento-col-5 { grid-column: span 5; }
        .bento-col-12 { grid-column: span 12; }
        .bento-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.6rem;
        }
        .bento-desc {
            color: #cbd5e1;
            font-size: 0.95rem;
        }
        .tool-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        .tool-tag {
            background: #0f3922;
            color: #d1fae5;
            font-size: 0.8rem;
            font-weight: 500;
            padding: 0.35rem 0.75rem;
            border-radius: 8px;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        /* Section Headings */
        .section-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        .section-header h2 {
            font-size: 2.2rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 0.5rem;
        }
        .section-header p {
            color: #94a3b8;
            font-size: 1rem;
        }
        /* Project Cards */
        .project-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
            margin-bottom: 5rem;
        }
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: 20px;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            transition: all 0.25s ease;
        }
        .card:hover {
            transform: translateY(-4px);
            border-color: var(--border-glow);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        .card-category {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--accent-green);
        }
        .card-badge {
            font-size: 0.7rem;
            background: rgba(34, 197, 94, 0.15);
            color: var(--accent-green);
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        .card h3 {
            font-size: 1.28rem;
            color: #ffffff;
            font-weight: 700;
            margin-bottom: 0.75rem;
            line-height: 1.35;
        }
        .card p {
            color: #cbd5e1;
            font-size: 0.92rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
            flex-grow: 1;
        }
        .card-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
            margin-bottom: 1.25rem;
        }
        .card-tag {
            font-size: 0.75rem;
            background: #092f1b;
            color: #a7f3d0;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
        }
        /* Experience Timeline */
        .timeline {
            max-width: 860px;
            margin: 0 auto 5rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .timeline-card {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: 20px;
            padding: 2rem;
        }
        .timeline-top {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }
        .timeline-role {
            font-size: 1.25rem;
            font-weight: 700;
            color: #ffffff;
        }
        .timeline-period {
            font-size: 0.85rem;
            color: var(--accent-green);
            font-weight: 600;
            background: rgba(16, 185, 129, 0.1);
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            border: 1px solid rgba(201, 247, 195, 0.2);
        }
        .timeline-company {
            color: #6ee7b7;
            font-size: 0.95rem;
            margin-bottom: 1rem;
            font-weight: 500;
        }
        .timeline-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }
        .timeline-list li {
            position: relative;
            padding-left: 1.4rem;
            color: #cbd5e1;
            font-size: 0.92rem;
        }
        .timeline-list li::before {
            content: "•";
            position: absolute;
            left: 0;
            color: var(--brand-green);
        }
        /* Certs & Education */
        .cert-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.25rem;
            margin-bottom: 5rem;
        }
        .cert-card {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.2s ease;
        }
        .cert-card:hover {
            border-color: var(--border-glow);
            transform: translateY(-2px);
        }
        .cert-issuer {
            font-size: 0.75rem;
            color: var(--accent-green);
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.05em;
        }
        .cert-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #ffffff;
            margin: 0.4rem 0;
        }
        .cert-period {
            font-size: 0.8rem;
            color: #94a3b8;
            margin-bottom: 0.6rem;
        }
        .cert-desc {
            font-size: 0.86rem;
            color: #cbd5e1;
        }
        /* Contact Section */
        .contact-box {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: 24px;
            padding: 2.5rem;
            max-width: 800px;
            margin: 0 auto 5rem;
        }
        .alert {
            padding: 0.85rem 1.25rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.92rem;
        }
        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.4);
            color: #a7f3d0;
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #fca5a5;
        }
        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.4rem;
            font-size: 0.85rem;
            color: #e2e8f0;
            font-weight: 500;
        }
        .form-control {
            width: 100%;
            background: #021209;
            border: 1px solid var(--border-card);
            border-radius: 10px;
            padding: 0.8rem 1rem;
            color: #ffffff;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s;
            font-family: inherit;
        }
        .form-control:focus {
            border-color: var(--brand-green);
        }
        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }
        /* Footer */
        footer {
            border-top: 1px solid var(--border-card);
            padding: 2.5rem 0;
            text-align: center;
            color: #64748b;
            font-size: 0.88rem;
        }
        /* Responsive */
        @media (max-width: 820px) {
            .navbar {
                width: 90%;
                padding: 0.5rem 1rem;
            }
            .nav-links {
                gap: 1rem;
                justify-content: space-around;
            }
            .bento-col-7, .bento-col-5 {
                grid-column: span 12;
            }
            .contact-box {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Floating Navigation (inspired by rcvb.info) -->
    <nav class="navbar" aria-label="Navigasi Utama">
        <ul class="nav-links">
            <li><a href="#about">About</a></li>
            <li><a href="#projects">Projects</a></li>
            <li><a href="#experience">Experience</a></li>
            <li><a href="#education">Education</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
    </nav>
    <main>
        <!-- Hero Section -->
        <section id="about" class="hero container">
            <!-- ponytail: avatar uses SVG placeholder; replace with photo in assets/avatar.jpg if needed. -->
            <div class="avatar-wrap">
                <div class="avatar" aria-hidden="true">RS</div>
            </div>
            <span class="pill-badge"><?= htmlspecialchars($profile['tagline']) ?></span>
            <h1>
                <span class="highlight" id="typewriter">Cloud &amp; DevOps Specialist</span>
            </h1>
            <p class="lead">
                Halo! Saya <strong><?= htmlspecialchars($profile['name']) ?></strong>, seorang <?= htmlspecialchars($profile['role']) ?> yang berdomisili di <?= htmlspecialchars($profile['location']) ?>. Berfokus pada cloud infrastructure, virtualisasi, network hardening, dan otomatisasi deployment.
            </p>
            <div class="btn-group">
                <a href="#projects" class="btn btn-primary">
                    Lihat Proyek
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                </a>
                <a href="resume.pdf" target="_blank" rel="noopener noreferrer" class="btn btn-outline">
                    View Resume
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>
        </section>
        <!-- Bento Grid Highlights -->
        <div class="container">
            <div class="bento-grid">
                <div class="bento-card bento-col-7">
                    <div>
                        <div class="bento-title">Telemetry, Hardening &amp; Resilience</div>
                        <div class="bento-desc">
                            Berpengalaman menangani insiden siber nyata, penelusuran root cause analysis (SSRF, malware persistence), pengamanan container Docker, serta implementasi firewall pfSense dan IDS/IPS Snort.
                        </div>
                    </div>
                    <div class="tool-tags">
                        <span class="tool-tag">Root Cause Analysis</span>
                        <span class="tool-tag">Container Isolation</span>
                        <span class="tool-tag">pfSense &amp; Snort</span>
                        <span class="tool-tag">Zero Downtime Migration</span>
                    </div>
                </div>
                <div class="bento-card bento-col-5">
                    <div>
                        <div class="bento-title">Hybrid &amp; Remote Ready</div>
                        <div class="bento-desc">
                            Siap berkolaborasi dalam tim distributed / remote maupun on-site. Berkomunikasi lancar dengan sertifikasi kemampuan bahasa Inggris TOEIC Skor 920.
                        </div>
                    </div>
                    <div class="tool-tags">
                        <span class="tool-tag">Indonesia (GMT+7)</span>
                        <span class="tool-tag">TOEIC 920</span>
                        <span class="tool-tag">Agile / CI/CD</span>
                    </div>
                </div>
                <div class="bento-card bento-col-12">
                    <div class="bento-title">Core Toolchain &amp; Technologies</div>
                    <div class="tool-tags">
                        <?php foreach ($tools as $tool): ?>
                            <span class="tool-tag"><?= htmlspecialchars($tool) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- Projects Section -->
        <section id="projects" class="container">
            <div class="section-header">
                <h2>Featured Projects</h2>
                <p>Portofolio implementasi cloud, keamanan siber, dan rekayasa perangkat lunak.</p>
            </div>
            <div class="project-grid">
                <?php foreach ($projects as $item): ?>
                    <article class="card">
                        <div class="card-header">
                            <span class="card-category"><?= htmlspecialchars($item['category']) ?></span>
                            <span class="card-badge"><?= htmlspecialchars($item['badge']) ?></span>
                        </div>
                        <h3><?= htmlspecialchars($item['title']) ?></h3>
                        <p><?= htmlspecialchars($item['desc']) ?></p>
                        <div class="card-tags">
                            <?php foreach ($item['tech'] as $tech): ?>
                                <span class="card-tag"><?= htmlspecialchars($tech) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
        <!-- Experience Section -->
        <section id="experience" class="container">
            <div class="section-header">
                <h2>Work Experience</h2>
                <p>Pengalaman profesional dalam administrasi sistem dan keamanan siber.</p>
            </div>
            <div class="timeline">
                <?php foreach ($experiences as $exp): ?>
                    <div class="timeline-card">
                        <div class="timeline-top">
                            <div class="timeline-role"><?= htmlspecialchars($exp['role']) ?></div>
                            <span class="timeline-period"><?= htmlspecialchars($exp['period']) ?></span>
                        </div>
                        <div class="timeline-company"><?= htmlspecialchars($exp['company']) ?></div>
                        <ul class="timeline-list">
                            <?php foreach ($exp['points'] as $pt): ?>
                                <li><?= htmlspecialchars($pt) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        <!-- Education & Certifications -->
        <section id="education" class="container">
            <div class="section-header">
                <h2>Certifications &amp; Education</h2>
                <p>Kompetensi tersertifikasi dan latar belakang akademik resmi.</p>
            </div>
            <div class="cert-grid">
                <?php foreach ($certs as $cert): ?>
                    <div class="cert-card">
                        <span class="cert-issuer"><?= htmlspecialchars($cert['issuer']) ?></span>
                        <h4 class="cert-title"><?= htmlspecialchars($cert['title']) ?></h4>
                        <div class="cert-period"><?= htmlspecialchars($cert['period']) ?></div>
                        <p class="cert-desc"><?= htmlspecialchars($cert['desc']) ?></p>
                    </div>
                <?php endforeach; ?>
                <?php foreach ($education as $edu): ?>
                    <div class="cert-card">
                        <span class="cert-issuer">PENDIDIKAN FORMAL</span>
                        <h4 class="cert-title"><?= htmlspecialchars($edu['institution']) ?></h4>
                        <div class="cert-period"><?= htmlspecialchars($edu['period']) ?> &bull; <?= htmlspecialchars($edu['meta']) ?></div>
                        <p class="cert-desc"><?= htmlspecialchars($edu['degree']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        <!-- Contact Section -->
        <section id="contact" class="container">
            <div class="section-header">
                <h2>Get In Touch</h2>
                <p>Silakan kirim pesan untuk peluang kerja sama, project, atau diskusi teknis.</p>
            </div>
            <div class="contact-box">
                <?php if ($feedback !== null): ?>
                    <div class="alert alert-<?= $feedbackType ?>" role="alert">
                        <?= htmlspecialchars($feedback) ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="#contact" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Nama Anda" required maxlength="100">
                    </div>
                    <div class="form-group">
                        <label for="email">Alamat Email</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="nama@perusahaan.com" required maxlength="100">
                    </div>
                    <div class="form-group">
                        <label for="message">Pesan</label>
                        <textarea id="message" name="message" class="form-control" placeholder="Tuliskan pesan atau penawaran kerja sama di sini..." required maxlength="3000"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                        Kirim Pesan
                    </button>
                </form>
                <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border-card); display: flex; flex-wrap: wrap; gap: 1.5rem; justify-content: center; font-size: 0.9rem;">
                    <a href="mailto:<?= htmlspecialchars($profile['email']) ?>" style="color: var(--accent-green); display: inline-flex; align-items: center; gap: 0.4rem;">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <?= htmlspecialchars($profile['email']) ?>
                    </a>
                    <a href="https://wa.me/6288226539328" target="_blank" rel="noopener noreferrer" style="color: var(--accent-green); display: inline-flex; align-items: center; gap: 0.4rem;">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        WhatsApp: <?= htmlspecialchars($profile['phone']) ?>
                    </a>
                    <a href="<?= htmlspecialchars($profile['linkedin']) ?>" target="_blank" rel="noopener noreferrer" style="color: var(--accent-green); display: inline-flex; align-items: center; gap: 0.4rem;">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        LinkedIn Profile
                    </a>
                </div>
            </div>
        </section>
    </main>
    <footer>
        <div class="container">
            &copy; <?= date('Y') ?> <?= htmlspecialchars($profile['name']) ?>. Built with PHP. All rights reserved.
        </div>
    </footer>
    <script>
        // Typewriter animation similar to rcvb.info hero
        (function() {
            const roles = [
                "Cloud & DevOps Specialist",
                "System & Virtualization Engineer",
                "DevSecOps & Cyber Security"
            ];
            let roleIdx = 0, charIdx = 0, deleting = false;
            const target = document.getElementById("typewriter");
            if (!target) return;
            function tick() {
                const current = roles[roleIdx];
                if (deleting) {
                    target.textContent = current.substring(0, charIdx--);
                } else {
                    target.textContent = current.substring(0, charIdx++);
                }
                let speed = deleting ? 40 : 80;
                if (!deleting && charIdx === current.length + 1) {
                    speed = 1800;
                    deleting = true;
                } else if (deleting && charIdx === 0) {
                    deleting = false;
                    roleIdx = (roleIdx + 1) % roles.length;
                    speed = 400;
                }
                setTimeout(tick, speed);
            }
            tick();
        })();
    </script>
</body>
</html>
