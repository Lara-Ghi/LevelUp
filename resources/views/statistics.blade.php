@extends('layouts.app')

@section('title', 'Statistics')

<!-- Main Content -->
@section('content')
    <main class="statistics-content">
        <header class="statistics-container">
            <h1>📊 Your statistics </h1>
        </header>

        <div class="stats-grid-container">
            <div class="stats-grid-container barchart">
                <canvas id="activityChart"></canvas>
            </div>
            <div class="stats-grid-container piechart">
                <canvas id="summaryPie"></canvas>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    @vite('resources/js/statistics.js')
@endsection