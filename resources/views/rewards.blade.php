@extends('layouts.app')

@section('additional_css')
    @vite('resources/css/rewards.css')
@endsection

@section('additional_js')
    @vite('resources/js/rewards.js')
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
                <!-- Add your available rewards content here -->
            @elseif(request()->query('tab') === 'saved')
                <!-- Saved Rewards Content -->
                <div class="rewards-grid" id="savedRewardsGrid">
                    <p class="no-saved-message">You haven't saved any rewards yet. Browse the "All" tab and click the heart icon to save your favorites!</p>
                </div>
            @elseif(request()->query('tab') === 'all')
                <!-- All Rewards Content -->
                 <!-- TODO: all rewards are hard-coded, they need to be changed and linked to the database, to create new cards, update or erase them -->
                <div class="rewards-grid">
                    <!-- Social Meeting-->
                    <div class="reward-card" data-reward-id="meeting">
                        <button class="save-btn" data-reward-id="meeting">
                            <img src="{{ asset('images/giftcards/heart_unchecked.png') }}" alt="Save" class="heart-icon">
                        </button>
                        <div class="reward-image">
                            <img src="{{ asset('images/giftcards/gift_meeting.png') }}" alt="Social Meeting">
                        </div>
                        <div class="reward-content">
                            <h3>Social Meeting</h3>
                            <p class="reward-description">Your ticket to a casual catch-up with your supervisor.</p>
                            <button class="redeem-btn" data-points="0">
                                <span class="btn-text">Not Yet</span>
                            </button>
                            <p class="reward-points">0 Points</p>
                        </div>
                    </div>

                    <!-- Coffee Card -->
                    <div class="reward-card" data-reward-id="coffee">
                        <button class="save-btn" data-reward-id="coffee">
                            <img src="{{ asset('images/giftcards/heart_unchecked.png') }}" alt="Save" class="heart-icon">
                        </button>
                        <div class="reward-image">
                            <img src="{{ asset('images/giftcards/gift_coffee.jpg') }}" alt="10 Coffee Card">
                        </div>
                        <div class="reward-content">
                            <h3>10 Coffee Card</h3>
                            <p class="reward-description">Fuel your day at the best café in town.</p>
                            <button class="redeem-btn" data-points="300">
                                <span class="btn-text">Not Yet</span>
                            </button>
                            <p class="reward-points">300 Points</p>
                        </div>
                    </div>

                    <!-- Spotify Subscription -->
                    <div class="reward-card" data-reward-id="spotify">
                        <button class="save-btn" data-reward-id="spotify">
                            <img src="{{ asset('images/giftcards/heart_unchecked.png') }}" alt="Save" class="heart-icon">
                        </button>
                        <div class="reward-image">
                            <img src="{{ asset('images/giftcards/gift_spotify.jpg') }}" alt="1 Month Spotify Subscription">
                        </div>
                        <div class="reward-content">
                            <h3>1 Month Spotify Subscription</h3>
                            <p class="reward-description">Soundtrack your life with unlimited music.</p>
                            <button class="redeem-btn" data-points="100">
                                <span class="btn-text">Not Yet</span>
                            </button>
                            <p class="reward-points">100 Points</p>
                        </div>
                    </div>

                    <!-- PureGym Subscription -->
                    <div class="reward-card" data-reward-id="puregym">
                        <button class="save-btn" data-reward-id="puregym">
                            <img src="{{ asset('images/giftcards/heart_unchecked.png') }}" alt="Save" class="heart-icon">
                        </button>
                        <div class="reward-image">
                            <img src="{{ asset('images/giftcards/gift_gym.png') }}" alt="1 Month PureGym Subscription">
                        </div>
                        <div class="reward-content">
                            <h3>1 Month PureGym Subscription</h3>
                            <p class="reward-description">Healthy body, healthy mind.</p>
                            <button class="redeem-btn" data-points="200">
                                <span class="btn-text">Not Yet</span>
                            </button>
                            <p class="reward-points">200 Points</p>
                        </div>
                    </div>

                    <!-- Netflix Subscription -->
                    <div class="reward-card" data-reward-id="netflix">
                        <button class="save-btn" data-reward-id="netflix">
                            <img src="{{ asset('images/giftcards/heart_unchecked.png') }}" alt="Save" class="heart-icon">
                        </button>
                        <div class="reward-image">
                            <img src="{{ asset('images/giftcards/gift_netflix.jpg') }}" alt="1 Month Netflix Subscription">
                        </div>
                        <div class="reward-content">
                            <h3>1 Month Netflix Subscription</h3>
                            <p class="reward-description">Well-deserved chill time.</p>
                            <button class="redeem-btn" data-points="150">
                                <span class="btn-text">Not Yet</span>
                            </button>
                            <p class="reward-points">150 Points</p>
                        </div>
                    </div>

                    <!-- Cinema Tickets -->
                    <div class="reward-card" data-reward-id="cinema">
                        <button class="save-btn" data-reward-id="cinema">
                            <img src="{{ asset('images/giftcards/heart_unchecked.png') }}" alt="Save" class="heart-icon">
                        </button>
                        <div class="reward-image">
                            <img src="{{ asset('images/giftcards/gift_cinema.png') }}" alt="2 Cinema Tickets (Kinorama)">
                        </div>
                        <div class="reward-content">
                            <h3>2 Cinema Tickets (Kinorama)</h3>
                            <p class="reward-description">Big screen, big moments.</p>
                            <button class="redeem-btn" data-points="250">
                                <span class="btn-text">Not Yet</span>
                            </button>
                            <p class="reward-points">250 Points</p>
                        </div>
                    </div>

                    <!-- Audible Subscription -->
                    <div class="reward-card" data-reward-id="audible">
                        <button class="save-btn" data-reward-id="audible">
                            <img src="{{ asset('images/giftcards/heart_unchecked.png') }}" alt="Save" class="heart-icon">
                        </button>
                        <div class="reward-image">
                            <img src="{{ asset('images/giftcards/gift_audible.png') }}" alt="1 Month Audible Subscription">
                        </div>
                        <div class="reward-content">
                            <h3>1 Month Audible Subscription</h3>
                            <p class="reward-description">Expand your horizons.</p>
                            <button class="redeem-btn" data-points="150">
                                <span class="btn-text">Not Yet</span>
                            </button>
                            <p class="reward-points">150 Points</p>
                        </div>
                    </div>

                    <!-- Spa Entry -->
                    <div class="reward-card" data-reward-id="spa">
                        <button class="save-btn" data-reward-id="spa">
                            <img src="{{ asset('images/giftcards/heart_unchecked.png') }}" alt="Save" class="heart-icon">
                        </button>
                        <div class="reward-image">
                            <img src="{{ asset('images/giftcards/gift_spa.png') }}" alt="1 Spa Entry at Sønderborg Alsik Spa">
                        </div>
                        <div class="reward-content">
                            <h3>1 Spa Entry at Sønderborg Alsik Spa</h3>
                            <p class="reward-description">Pure relaxation awaits.</p>
                            <button class="redeem-btn" data-points="500">
                                <span class="btn-text">Not Yet</span>
                            </button>
                            <p class="reward-points">500 Points</p>
                        </div>
                    </div>
                </div>
            @elseif(request()->query('tab') === 'history')
                <!-- History Content -->
                <!-- Add your history content here -->
            @endif
        </div>
    </main>
@endsection