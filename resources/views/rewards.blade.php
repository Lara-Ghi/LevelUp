@extends('layouts.app')

@section('additional_css')
    @vite('resources/css/rewards.css')
@endsection

@section('title', 'Rewards')

@section('content')
    <!-- Rewards Sub-Navigation -->
    <div class="rewards-nav">
        <a href="{{ url('/rewards?tab=available') }}"
            class="rewards-nav-link {{ request()->query('tab', 'available') === 'available' ? 'active' : '' }}">
            <i class="fas fa-gift"></i>
            Available
        </a>
        <a href="{{ url('/rewards?tab=saved') }}"
            class="rewards-nav-link {{ request()->query('tab') === 'saved' ? 'active' : '' }}">
            <i class="fas fa-bookmark"></i>
            Saved
        </a>
        <a href="{{ url('/rewards?tab=all') }}"
            class="rewards-nav-link {{ request()->query('tab') === 'all' ? 'active' : '' }}">
            <i class="fas fa-list"></i>
            All
        </a>
        <a href="{{ url('/rewards?tab=history') }}"
            class="rewards-nav-link {{ request()->query('tab') === 'history' ? 'active' : '' }}">
            <i class="fas fa-history"></i>
            History
        </a>
    </div>

    <main class="content">
        <!-- Content Area -->
        <div class="rewards-content">
            @if(request()->query('tab', 'available') === 'available')
                <!-- Available Rewards Content -->
                <h2>Available Rewards</h2>
                <!-- Add your available rewards content here -->
            @elseif(request()->query('tab') === 'saved')
                <!-- Saved Rewards Content -->
                <h2>Saved Rewards</h2>
                <!-- Add your saved rewards content here -->
            @elseif(request()->query('tab') === 'all')
                <!-- All Rewards Content -->
                <h2>All Rewards</h2>
                <!-- Add your all rewards content here -->
            @elseif(request()->query('tab') === 'history')
                <!-- History Content -->
                <h2>Redemption History</h2>
                <!-- Add your history content here -->
            @endif
        </div>
    </main>
@endsection