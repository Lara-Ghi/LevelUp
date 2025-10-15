@extends('layouts.app')

@section('title', 'Home')

<!-- Main Content -->
@section('content')
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
@endsection
<!-- Pomodoro JavaScript Files -->
<!-- Focus Clock JavaScript - Enhanced Version -->
@section('scripts')
  @vite('resources/js/home-clock/focus-clock.js')
@endsection