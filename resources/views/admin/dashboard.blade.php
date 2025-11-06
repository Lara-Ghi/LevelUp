@extends('layouts.app')

@section('title', 'Control Dashboard')

@section('content')
<div class="auth-page admin-dashboard">
  <div class="auth-content">
    <div class="login-container">

      <h1 class="text page-title">Control Dashboard</h1>

      @foreach (['success', 'error', 'info'] as $msg)
        @if (session($msg))
          <div class="alert alert-{{ $msg === 'error' ? 'danger' : $msg }}">{{ session($msg) }}</div>
        @endif
      @endforeach
      @if ($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
      @endif

      <div class="dashboard-grid">
        {{-- LEFT COLUMN: User Form --}}
        <div class="login-card">
          <div class="text section-title">User Form</div>

          @if(isset($editUser))
            <form method="POST" action="{{ route('admin.users.update', $editUser) }}" class="form">
              @csrf @method('PATCH')
              @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif

              <div class="d-flex flex-wrap" style="gap:16px;">
                <div class="login-data" style="flex:1 1 260px;">
                  <label>First Name *</label>
                  <input type="text" name="name" value="{{ old('name', $editUser->name) }}" required>
                </div>
                <div class="login-data" style="flex:1 1 260px;">
                  <label>Last Name *</label>
                  <input type="text" name="surname" value="{{ old('surname', $editUser->surname) }}" required>
                </div>
              </div>

              <div class="d-flex flex-wrap" style="gap:16px;">
                <div class="login-data" style="flex:1 1 260px;">
                  <label>Username *</label>
                  <input type="text" name="username" value="{{ old('username', $editUser->username) }}" required>
                </div>
                <div class="login-data" style="flex:1 1 260px;">
                  <label>Date of Birth</label>
                  <input type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($editUser->date_of_birth)->format('Y-m-d')) }}">
                </div>
              </div>

              <div class="d-flex flex-wrap" style="gap:16px;">
                <div class="login-data" style="flex:1 1 260px;">
                  <label>Sitting Position</label>
                  <input type="number" name="sitting_position" min="0" max="65535" value="{{ old('sitting_position', $editUser->sitting_position) }}">
                </div>
                <div class="login-data" style="flex:1 1 260px;">
                  <label>Standing Position</label>
                  <input type="number" name="standing_position" min="0" max="65535" value="{{ old('standing_position', $editUser->standing_position) }}">
                </div>
              </div>

              <div class="loginpage-btn" style="margin-top:12px;">
                <button type="submit">Save Changes</button>
              </div>
            </form>
          @else
            <form method="POST" action="{{ route('admin.users.store') }}" class="form">
              @csrf

              <div class="d-flex flex-wrap" style="gap:16px;">
                <div class="login-data" style="flex:1 1 260px;">
                  <label>First Name *</label>
                  <input type="text" name="name" value="{{ old('name') }}" required>
                </div>
                <div class="login-data" style="flex:1 1 260px;">
                  <label>Last Name *</label>
                  <input type="text" name="surname" value="{{ old('surname') }}" required>
                </div>
              </div>

              <div class="d-flex flex-wrap" style="gap:16px;">
                <div class="login-data" style="flex:1 1 260px;">
                  <label>Username *</label>
                  <input type="text" name="username" value="{{ old('username') }}" required>
                </div>
                <div class="login-data" style="flex:1 1 260px;">
                  <label>Date of Birth</label>
                  <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}">
                </div>
              </div>

              <div class="d-flex flex-wrap" style="gap:16px;">
                <div class="login-data" style="flex:1 1 260px;">
                  <label>Password *</label>
                  <input type="password" name="password" required>
                </div>
                <div class="login-data" style="flex:1 1 260px;">
                  <label>Confirm Password *</label>
                  <input type="password" name="password_confirmation" required>
                </div>
              </div>

              <div class="d-flex flex-wrap" style="gap:16px;">
                <div class="login-data" style="flex:1 1 260px;">
                  <label>Sitting Position</label>
                  <input type="number" name="sitting_position" min="0" max="65535" value="{{ old('sitting_position') }}">
                </div>
                <div class="login-data" style="flex:1 1 260px;">
                  <label>Standing Position</label>
                  <input type="number" name="standing_position" min="0" max="65535" value="{{ old('standing_position') }}">
                </div>
              </div>

              <div class="loginpage-btn" style="margin-top:12px;">
                <button type="submit">Create User</button>
              </div>
            </form>
          @endif
        </div>

        {{-- RIGHT COLUMN: Search + User Cards --}}
        <div class="login-card">
          <form method="GET" action="{{ route('admin.dashboard') }}" class="form big-search" style="margin-bottom: 2rem;">
            <div class="login-data">
              <input type="text" name="q" placeholder="Search by name/surname/username..." value="{{ request('q') }}">
            </div>
            <div class="loginpage-btn">
              <button type="submit">Search</button>
            </div>
          </form>

          <div class="text section-title">Users ({{ $users->total() }})</div>

          @if($users->isEmpty())
            <p class="text-center">No users found.</p>
          @else
            <div class="userlist-header">
              <div>Name</div>
              <div>Surname</div>
              <div>Username</div>
              <div>Role</div>
            </div>

            <div class="user-cards">
              @foreach($users as $user)
                <div class="user-card">
                  <div class="user-info-row">
                    <div>{{ $user->name }}</div>
                    <div>{{ $user->surname }}</div>
                    <div>{{ $user->username }}</div>
                    <div>
                      @if($user->role === 'admin')
                        <span class="badge bg-warning text-dark" style="font-size:0.9rem;">admin</span>
                      @else
                        <span class="badge bg-secondary" style="font-size:0.9rem;">user</span>
                      @endif
                    </div>
                  </div>

                  <div class="user-actions">
                    <a href="{{ route('admin.dashboard', array_filter(['q' => request('q'), 'edit' => $user->user_id])) }}"
                       class="btn-edit btn-compact"
                       title="Edit user">
                      <button type="button">
                        <i class="fa-solid fa-pen"></i>
                      </button>
                    </a>

                    @if($user->role === 'admin')
                    <form action="{{ route('admin.users.demote', $user) }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="loginpage-btn btn-compact">
                        <button type="submit"
                                class="demote-btn"
                                {{ auth()->id() === $user->user_id ? 'disabled' : '' }}
                                title="{{ auth()->id() === $user->user_id ? 'Cannot demote yourself' : 'Demote to user' }}">
                            Demote
                        </button>
                        </div>
                    </form>
                    @else
                    <form action="{{ route('admin.users.promote', $user) }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="loginpage-btn btn-compact">
                        <button type="submit" title="Promote to admin">
                            Promote
                        </button>
                        </div>
                    </form>
                    @endif

                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                      @csrf @method('DELETE')
                      <div class="loginpage-btn btn-compact">
                        <button type="submit"
                                {{ auth()->id() === $user->getKey() ? 'disabled' : '' }}
                                title="{{ auth()->id() === $user->getKey() ? 'Cannot delete yourself' : 'Delete user' }}">
                          Delete
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
              @endforeach
            </div>

            <div class="mt-3 text-center">
              {{ $users->links() }}
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
