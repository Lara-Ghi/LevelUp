@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <main class="profile-container">

        <header class="page-header">
            <h1> <span>My Profile</span></h1>
            <p class="page-subtitle">View your personal info and table preferences</p>
        </header>

        <div class="profile-card">
            
            <div class="profile-header">
                <img id="userPhoto" src="{{ asset('images/users/default.jpg') }}" class="profile-avatar">
                
                <div class="profile-ident">
                    <div class="profile-handle">{{ '@' . ($user->username ?? 'unknown') }}</div>
                </div>

                <button class="edit-btn">
                    ✏️ Edit
                </button>
            </div>

            <dl class="profile-dl">
                <dt>Full Name</dt>
                <dd>{{ $user->name }}{{ $user->surname ? ' ' . $user->surname : '' }}</dd>
                <dt>Date of Birth</dt>
                <dd>{{ $user->date_of_birth ? $user->date_of_birth->format('F j, Y') : 'Not set' }}</dd>
            </dl>
        </div>
        
        <div class="profile-card">
            <div class="table-settings">
                <h2>🪑 Table Height Preferences</h2>

                <div class="table-grid">
                    <div class="height-setting">
                        <label>Standing Height</label>
                        <div class="height-input">
                            <span style="font-size: 1.25rem; font-weight: 500; color: black;">
                                {{ $user->standing_position ?? 'Not set' }}
                            </span>
                            @if($user->standing_position)
                                <span class="unit">cm</span>
                            @endif
                        </div>
                    </div>

                    <div class="height-setting">
                        <label>Sitting Height</label>
                        <div class="height-input">
                            <span style="font-size: 1.25rem; font-weight: 500; color: black;">
                                {{ $user->sitting_position ?? 'Not set' }}
                            </span>
                            @if($user->sitting_position)
                                <span class="unit">cm</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection