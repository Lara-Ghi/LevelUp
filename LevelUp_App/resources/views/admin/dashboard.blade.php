@extends('layouts.app')

@section('title', 'Control Dashboard')

@section('content')
  <div class="auth-page admin-dashboard">
    <div class="auth-content">
      <div class="login-container">

        <h1 class="text page-title">Control Dashboard</h1>

        <!-- Dashboard Sub-Navigation -->
        <div class="rewards-nav" style="margin-bottom: 2rem;">
          <a href="{{ route('admin.dashboard', ['tab' => 'users']) }}"
            class="rewards-nav-link {{ request()->query('tab', 'users') === 'users' ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            Manage Users
          </a>
          <a href="{{ route('admin.dashboard', ['tab' => 'rewards']) }}"
            class="rewards-nav-link {{ request()->query('tab') === 'rewards' ? 'active' : '' }}">
            <i class="fas fa-gift"></i>
            Manage Rewards
          </a>
        </div>

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

        @if(request()->query('tab', 'users') === 'users')
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
                      <input type="date" name="date_of_birth"
                        value="{{ old('date_of_birth', optional($editUser->date_of_birth)->format('Y-m-d')) }}">
                    </div>
                  </div>

                  <div class="d-flex flex-wrap" style="gap:16px;">
                    <div class="login-data" style="flex:1 1 260px;">
                      <label>Sitting Position</label>
                      <input type="number" name="sitting_position" min="0" max="65535"
                        value="{{ old('sitting_position', $editUser->sitting_position) }}">
                    </div>
                    <div class="login-data" style="flex:1 1 260px;">
                      <label>Standing Position</label>
                      <input type="number" name="standing_position" min="0" max="65535"
                        value="{{ old('standing_position', $editUser->standing_position) }}">
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
                      <input type="number" name="standing_position" min="0" max="65535"
                        value="{{ old('standing_position') }}">
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
              <form method="GET" action="{{ route('admin.dashboard') }}" class="form big-search"
                style="margin-bottom: 2rem;">
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
                          class="btn-edit btn-compact" title="Edit user">
                          <button type="button">
                            <i class="fa-solid fa-pen"></i>
                          </button>
                        </a>

                        @if($user->role === 'admin')
                          <form action="{{ route('admin.users.demote', $user) }}" method="POST">
                            @csrf @method('PATCH')
                            <div class="loginpage-btn btn-compact">
                              <button type="submit" class="demote-btn" {{ auth()->id() === $user->user_id ? 'disabled' : '' }}
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
                            <button type="submit" {{ auth()->id() === $user->getKey() ? 'disabled' : '' }}
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

        @elseif(request()->query('tab') === 'rewards')
          <!-- Manage Rewards Content -->
          <div style="padding: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
              <h2 style="margin: 0;">All Rewards ({{ $rewards->count() }})</h2>
              <div style="display: flex; gap: 1rem;">
                <a href="{{ route('rewards.create') }}" class="loginpage-btn" style="display: inline-block;">
                  <button type="button">
                    <i class="fas fa-plus"></i> Add Reward
                  </button>
                </a>
                <button type="button" class="loginpage-btn" id="toggleArchivedBtn" style="background-color: #6c757d;">
                  <i class="fas fa-archive"></i> See Archived Rewards
                </button>
              </div>
            </div>

            <!-- Active Rewards Grid -->
            <div class="rewards-grid"
              style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
              @forelse($rewards as $reward)
                        <div class="reward-card" style="position: relative; border: 1px solid #ddd; border-radius: 8px; padding: 1rem; background:
                white;">
                          <div class="admin-controls"
                            style="position: absolute; top: 0.5rem; right: 0.5rem; display: flex; gap: 0.5rem;">
                            <a href="{{ route('rewards.edit', $reward->id) }}" class="admin-btn edit-btn" title="Edit" style="padding: 0.25rem
                0.5rem; background: #007bff; color: white; border-radius: 4px; text-decoration: none;">
                              <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="admin-btn archive-btn" data-reward-id="{{ $reward->id }}" title="Archive"
                              style="padding:
                0.25rem 0.5rem; background: #ffc107; color: black; border: none; border-radius: 4px; cursor: pointer;">
                              <i class="fas fa-archive"></i>
                            </button>
                          </div>

                          <div class="reward-image" style="margin-top: 2rem; text-align: center;">
                            <img
                              src="{{ $reward->card_image ? asset($reward->card_image) : asset('images/giftcards/placeholder.png') }}"
                              alt="{{ $reward->card_name }}" style="max-width: 100%; height: 150px; object-fit: contain;">
                          </div>

                          <div class="reward-content" style="margin-top: 1rem;">
                            <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem;">{{ $reward->card_name }}</h3>
                            <p style="font-size: 0.9rem; color: #666; margin-bottom: 0.5rem;">
                              {{ Str::limit($reward->card_description, 80) }}</p>
                            <p style="font-weight: bold; color: #007bff;">{{ $reward->points_amount }} Points</p>
                          </div>
                        </div>
              @empty
                <p style="grid-column: 1 / -1; text-align: center; color: #999;">No rewards yet. Click "Add Reward" to create
                  one!</p>
              @endforelse
            </div>

            <!-- Archived Rewards Section (hidden by default) -->
            <div id="archivedRewardsSection"
              style="display: none; margin-top: 3rem; padding-top: 2rem; border-top: 2px solid #ddd;">
              <h2 style="margin-bottom: 1.5rem;">Archived Rewards</h2>
              <div class="rewards-grid"
                style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
                <p style="grid-column: 1 / -1; text-align: center; color: #999;">No archived rewards yet.</p>
              </div>
            </div>
          </div>

          <script>
            // Toggle archived rewards visibility
            document.getElementById('toggleArchivedBtn')?.addEventListener('click', function () {
              const section = document.getElementById('archivedRewardsSection');
              if (section.style.display === 'none') {
                section.style.display = 'block';
                this.innerHTML = '<i class="fas fa-eye-slash"></i> Hide Archived Rewards';
              } else {
                section.style.display = 'none';
                this.innerHTML = '<i class="fas fa-archive"></i> See Archived Rewards';
              }
            });
          </script>
        @endif
      </div>
    </div>
  </div>
@endsection