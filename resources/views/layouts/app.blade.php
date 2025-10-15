<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LevelUp - @yield('title', 'Home')</title>
    
    <!-- External Resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⬆️</text></svg>">

    @vite('resources/css/app.css')
    @vite('resources/css/home-clock/focus-clock.css')

    @yield('additional_css')

    @yield('additional_js')
</head>
<body>
    @include('layouts.navigation')

    <main>
        @yield('content')
    </main>

    @include('layouts.desk-setup')

    <footer class="glass-footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-info">
                    <span>Made by Group 3 SDU Software Students © <span id="currentYear">{{ date('Y') }}</span> <strong>LevelUp</strong>. All rights reserved.</span>
                </div>
            </div>
        </div>
    </footer>

    @yield('scripts')

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
            
            console.log('⬆️ LevelUp - Ready!');
        });
    </script>
</body>
</html>