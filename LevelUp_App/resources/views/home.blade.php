@extends('layouts.app')

@section('title', 'Home')

<!-- Main Content -->
@section('content')
    <main class="content @guest guest-background @endguest">
        <div class="welcome-container">
            @auth
                {{-- Welcome message for logged-in users --}}
                <div class="welcome-text"><span class="welcome-purple">Welcome back,</span> <span class="user-highlight">{{ auth()->user()->name }}</span></div>
                <div class="welcome-subtitle">Ready to level up your health today?</div>
            @else
                {{-- Welcome message for guests --}}
                <div class="welcome-text"><span class="welcome-purple">Welcome to</span> <span class="user-highlight">LevelUp</span></div>
                <div class="welcome-subtitle">Stand up for your health! Please log in to start tracking your progress.</div>
            @endauth
            
            <div class="github-pill-container">
                <a href="https://github.com/Lara-Ghi/LevelUp" class="github-pill" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-github"></i>
                    Made by the wonderful Group 3 - LevelUp
                </a>
            </div>
        </div>
    </main>
@endsection

@auth
    <!-- Focus Clock JavaScript - Only for authenticated users -->
    @section('scripts')
      @vite('resources/js/home-clock/focus-clock.js')
    @endsection
@endauth