@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <main class="profile-container">

        <header class="page-header">
            <h1>👤 <span>My Profile</span></h1>
            <p class="page-subtitle">Manage your personal info and table preferences</p>
        </header>

        <div class="profile-card">
            
            <div class="profile-header">
                <img src="{{ asset('images/default-avatar.png') }}" class="profile-avatar">
                
                <div class="profile-ident">
                    <div class="profile-handle">@testusername</div>
                    <div class="profile-bio">“Just a guy who likes adjustable desks.”</div>
                </div>

                <button class="edit-btn">
                    ✏️ Edit
                </button>
            </div>

            <dl class="profile-dl">
                <dt>Full Name</dt>
                <dd>Test Name</dd>
                <dt>Email</dt>
                <dd>test@usermail.com</dd>
            </dl>

            <div class="table-settings">
                <h2>🪑 Table Settings</h2>

                <div class="table-grid">
                    <div class="height-setting">
                        <label>Standing Height</label>
                        <div class="height-input">
                            <input type="number" value="120" min="60" max="200">
                            <span class="unit">cm</span>
                        </div>
                    </div>

                    <div class="height-setting">
                        <label>Sitting Height</label>
                        <div class="height-input">
                            <input type="number" value="75" min="40" max="120">
                            <span class="unit">cm</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection