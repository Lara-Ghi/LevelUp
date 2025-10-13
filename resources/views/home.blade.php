<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LevelUp - Home</title>
    
    <!-- External Resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Focus Clock CSS -->
    @vite('resources/css/home-clock/focus-clock.css')
    
    <!-- Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🚀</text></svg>">

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Update active navigation link based on current page
            const currentPage = window.location.pathname;
            const navLinks = document.querySelectorAll('.nav-link');
            
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === currentPage || (currentPage === '/' && link.textContent.trim() === 'Home')) {
                    link.classList.add('active');
                }
            });
            
            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
            
            // Update current year in footer copyright
            const currentYearElement = document.getElementById('currentYear');
            if (currentYearElement) {
                const currentYear = new Date().getFullYear();
                currentYearElement.textContent = currentYear;
            }
            
            // Welcome animation enhancement
            setTimeout(() => {
                const welcomeText = document.querySelector('.welcome-text');
                const welcomeSubtitle = document.querySelector('.welcome-subtitle');
                
                if (welcomeText) welcomeText.style.opacity = '1';
                if (welcomeSubtitle) welcomeSubtitle.style.opacity = '1';
            }, 300);
            
            console.log('🚀 LevelUp Enhanced Volcanic Navbar - Ready!');
        });
    </script>
</head>
<body>
    <!-- Enhanced Volcanic-Inspired Modern Navigation -->
    <header>
        <nav class="modern-nav">
            <!-- Enhanced Volcanic Bubble Effects - More Blue Circles -->
            <div class="nav-bubble-container">
                <div class="nav-gradient-container">
                    <div class="nav-g1"></div>
                    <div class="nav-g2"></div>
                    <div class="nav-g3"></div>
                    <div class="nav-g4"></div>
                    <div class="nav-g5"></div>
                    <div class="nav-g6"></div>
                    <div class="nav-g7"></div>
                    <div class="nav-g8"></div>
                    <div class="nav-g9"></div>
                    <div class="nav-g10"></div>
                    <div class="nav-g11"></div>
                    <div class="nav-g12"></div>
                    <div class="nav-g13"></div>
                    <div class="nav-g14"></div>
                    <div class="nav-g15"></div>
                </div>
            </div>
            
            <div class="nav-container">
                <!-- Logo -->
                <div class="nav-logo">
                    <a href="{{ url('/') }}" aria-label="Home">
                        <img src="{{ asset('nav-logo.png') }}" alt="LevelUp Logo" class="nav-logo-img">
                    </a>
                </div>
                
                <!-- Navigation Links -->
                <div class="nav-links">
                    <a href="{{ url('/') }}" class="nav-link active">
                        <i class="fa-solid fa-house"></i>
                        Home
                    </a>
                    <a href="{{ url('/statistics') }}" class="nav-link">
                        <i class="fas fa-chart-bar"></i>
                        Statistics
                    </a>
                    <a href="{{ url('/rewards') }}" class="nav-link">
                        <i class="fas fa-trophy"></i>
                        Rewards
                    </a>
                </div>

                <!-- Enhanced User Actions -->
                <div class="nav-actions">
                    <!-- Simple Blue Points Display -->
                    <div class="points-display">
                        <div>
                            <i class="fas fa-star"></i>
                            <span class="points-number" id="totalPoints">0</span>
                            <span class="points-label">Points</span>
                        </div>
                        <span class="points-daily" id="dailyPoints" style="font-size: 0.75rem; color: rgba(255,255,255,0.8);">0/100 today</span>
                    </div>
                    <a href="{{ url('/profile') }}" class="profile-link">
                        <i class="fa-solid fa-user"></i>
                        Profile
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="content">
        <div class="welcome-container">
            <div class="welcome-text"><span class="welcome-purple">Welcome</span> <span class="user-highlight">Group 3</span></div>
            <div class="welcome-subtitle">Stand up for your health!</div>
            
            <div class="github-pill-container">
                <a href="https://github.com/Lara-Ghi/LevelUp" class="github-pill" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-github"></i>
                    Made by the wonderful Group 3 - LevelUp
                </a>
            </div>
        </div>
    </main>

    <!-- Desk Setup - Above Footer -->
    <div class="desk-setup">
        <div class="icons">
            <svg width="200" height="160" class="laptop">
                <path class="screen"
                      d="
                        M 20,20
                        L 180,20
                        C 185,20 185,20 185,30
                        L 185,130
                        C 185,135 185,135 175,135
                        L 20,135
                        C 10,135 10,135 10,130
                        L 10,25
                        C 10,20 10,20 20,20
                        "/>
                <path class="body"
                      d="
                        M 10,135
                        L 185,135 185,145 10,145 10,135 15,135
                        "      
                      />
                <path class="logo"
                      d="
                        M 90,86
                        C 90,71 110,71 110,85
                        M 110,85
                        C 110,99 90,99 90,85
                        "      
                      />
            </svg>

            <svg width="100" height="100" class="mug">
                <path class="body"
                  d="
                    M 25,35
                    C 35,30 55,30 65,35
                    M 65,35
                    C 55,40 35,40 25,35
                    L 25,75
                    C 35,80 55,80 65,75
                    L 65,35
                  "
                />
                <path class="handle"
                  d="
                    M 65,40
                    C 85,40 85,70 65,70
                    L 65,65
                    C 78,65 78,45 65,45
                    L 65,40
                  "
                />
            </svg>

            <svg width="180" height="100" class="notebook">
                <path class="pad"
                  d="
                    M 20,50
                    L 105,35 160,60 70,80 20,50
                    L 20,55 70,85 70,80 20,50
                    M 161,60
                    L 161,65 70,85 70,80 161,60
                  "
                />
                <path class="label"
                  d="
                    M 105,43
                    L 130,56 102,61 82,47 105,43
                  "
                />
                <path class="spring"
                  d="
                    M 20,50
                    C 16,46 26,44 30,48
                    C 26,44 36,42 40,46
                    C 36,42 46,41 50,45
                    C 46,41 56,39 60,43
                    C 56,39 66,37 70,41
                    C 66,37 76,35 80,39
                    C 76,35 86,34 90,38
                    C 86,34 96,32 100,36
                  "      
                />
            </svg>

            <svg width="80" height="120" class="pens">
                <path class="case"
                  d="
                    M 20,60
                    L 60,60 60,65 20,65 20,60
                    M 20,65
                    L 20,100
                    C 20,105 20,105 25,105
                    L 55,105
                    C 60,105 60,105 60,100
                    L 60,65
                  "
                />
                <path class="pen-1"
                  d="
                    M 30,60
                    L 30,30 35,30 35,60 30,60
                    M 30,33
                    L 27,33 27,55 30,55 30,33
                  "
                />
                <path class="pen-2"
                  d="
                    M 38,60
                    L 38,35 40,31 42,35 42,60 38,60
                  "
                />
                <path class="pen-3"
                  d="
                    M 46,60
                    L 46,28 53,28 53,60 46,60
                    M 46,45
                    L 53,45
                  "
                />
            </svg>

            <svg width="140" height="140" class="calendar">
                <path class="pad"
                  d="
                    M 30,30
                    L 115,30 120,120 95,120 25,120 30,30
                    M 113,30
                    L 110,120
                    M 30,45 112,45
                    M 28,80 112,80
                    M 37,80 35,120
                    M 52,80 50,120
                    M 67,80 65,120
                    M 82,80 80,120
                    M 97,80 95,120
                    M 27,94 112,94
                    M 26,107 111,107
                  "      
                />
                <path class="spring"
                  d="
                    M 32,30
                    C 32,20 52,20 52,30
                    M 52,30
                    C 52,20 72,20 72,30
                    M 72,30
                    C 72,20 92,20 92,30
                    M 92,30
                    C 92,20 112,20 112,30
                  "      
                />
            </svg>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="glass-footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-info">
                    <span>Made by Group 3 SDU Software Students © <span id="currentYear">2025</span> <strong>LevelUp</strong>. All rights reserved.</span>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Pomodoro JavaScript Files -->
    <!-- Focus Clock JavaScript - Enhanced Version -->
    @vite('resources/js/home-clock/focus-clock.js')
</body>
</html>