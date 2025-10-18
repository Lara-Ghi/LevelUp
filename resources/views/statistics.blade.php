@extends('layouts.app')

@section('title', 'Statistics')

<!-- Main Content -->
@section('content')
    <main class="statistics-content">
        <div class="statistics-container">
            <h1>📊 Your statistics </h1>
        </div>
        
        <div class="row text-center mb-4">
            <div class="col-md-4">
                <div class="card p-3 shadow-sm">
                    <h4>Total Sitting Time</h4>
                    <p class="fs-4 fw-bold text-primary">640 min</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 shadow-sm">
                    <h4>Total Standing Time</h4>
                    <p class="fs-4 fw-bold text-success">310 min</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 shadow-sm">
                    <h4>Total Cycles</h4>
                    <p class="fs-4 fw-bold text-secondary">31</p>
                </div>
            </div>
        </div>

    {{-- Charts --}}
        <div class="row">
            <div class="col-md-8">
                <div class="card p-3 shadow-sm mb-4">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 shadow-sm mb-4">
                    <canvas id="summaryPie"></canvas>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    @vite('resources/js/statistics.js')
@endsection