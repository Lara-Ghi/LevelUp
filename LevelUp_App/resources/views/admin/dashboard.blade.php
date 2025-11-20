@extends('layouts.app')

@section('title', 'Control Dashboard')

@section('additional_css')
    @vite('resources/css/rewards.css')
@endsection

@section('additional_js')
    @vite('resources/js/admin-dashboard.js')
@endsection

@section('content')
  <!-- Dashboard Sub-Navigation -->
  <div class="rewards-nav">
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

  <div class="auth-page admin-dashboard">
    <div class="auth-content">
      <div class="login-container">

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
          <div class="dashboard-grid">
            {{-- LEFT COLUMN: Reward Form --}}
            <div class="login-card">
              <div class="text section-title">Reward Form</div>

              @if(isset($editReward))
                <form method="POST" action="{{ route('admin.rewards.update', $editReward) }}" enctype="multipart/form-data" class="form">
                  @csrf @method('PUT')
                  @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif

                  <div class="login-data">
                    <label>Reward Name *</label>
                    <input type="text" name="card_name" value="{{ old('card_name', $editReward->card_name) }}" required>
                  </div>

                  <div class="login-data">
                    <label>Points Amount *</label>
                    <input type="number" name="points_amount" min="0" value="{{ old('points_amount', $editReward->points_amount) }}" required>
                  </div>

                  <div class="login-data">
                    <label>Description</label>
                    <textarea name="card_description" rows="3">{{ old('card_description', $editReward->card_description) }}</textarea>
                  </div>

                  <div class="login-data">
                    <label>Reward Image</label>
                    <input type="file" name="card_image" accept="image/*">
                    @if($editReward->card_image)
                      <div style="margin-top: 8px;">
                        <img src="{{ asset($editReward->card_image) }}" alt="Current image" style="width: 100px; height: 60px; object-fit: cover;">
                        <p style="font-size: 0.85em; color: #666;">Current image</p>
                      </div>
                    @endif
                  </div>

                  <div class="loginpage-btn" style="margin-top:12px;">
                    <button type="submit">Save Changes</button>
                  </div>
                </form>
              @else
                <form method="POST" action="{{ route('admin.rewards.store') }}" enctype="multipart/form-data" class="form">
                  @csrf

                  <div class="login-data">
                    <label>Reward Name *</label>
                    <input type="text" name="card_name" value="{{ old('card_name') }}" required>
                  </div>

                  <div class="login-data">
                    <label>Points Amount *</label>
                    <input type="number" name="points_amount" min="0" value="{{ old('points_amount') }}" required>
                  </div>

                  <div class="login-data">
                    <label>Description</label>
                    <textarea name="card_description" rows="3">{{ old('card_description') }}</textarea>
                  </div>

                  <div class="login-data">
                    <label>Reward Image</label>
                    <input type="file" name="card_image" accept="image/*">
                    <p style="font-size: 0.85em; color: #666; margin-top: 4px;">Optional: Upload an image for the reward card</p>
                  </div>

                  <div class="loginpage-btn" style="margin-top:12px;">
                    <button type="submit">Create Reward</button>
                  </div>
                </form>
              @endif
            </div>

            {{-- RIGHT COLUMN: Rewards List --}}
            <div class="login-card">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <div class="text section-title">Active Rewards</div>
                <button type="button" class="loginpage-btn" id="toggleArchivedBtn" style="background-color: #6c757d;">
                  <i class="fas fa-archive"></i> Show Archived
                </button>
              </div>

              @if($activeRewards && $activeRewards->isEmpty())
                <p style="text-align: center; color: #999;">No active rewards found.</p>
              @else
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                  @foreach($activeRewards ?? [] as $reward)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: #f8f9fa; border-radius: 8px; border: 1px solid #dee2e6;">
                      <div style="display: flex; align-items: center; gap: 1rem; flex: 1;">
                        <img src="{{ $reward->card_image ? asset($reward->card_image) : asset('images/giftcards/placeholder.png') }}" 
                            alt="{{ $reward->card_name }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                        <div>
                          <h4 style="margin: 0 0 0.25rem 0; font-size: 1.1em;">{{ $reward->card_name }}</h4>
                          <p style="color: #666; font-size: 0.9em; margin: 0.25rem 0;">{{ Str::limit($reward->card_description ?? '', 50) }}</p>
                          <p style="color: #007bff; font-weight: bold; margin: 0.25rem 0;">{{ $reward->points_amount }} Points</p>
                        </div>
                      </div>

                      <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <a href="{{ route('admin.dashboard', array_filter(['tab' => 'rewards', 'q' => request('q'), 'edit_reward' => $reward->id])) }}" 
                          class="btn-edit btn-compact" title="Edit reward">
                          <button type="button">
                            <i class="fas fa-edit"></i>
                          </button>
                        </a>

                        <form action="{{ route('admin.rewards.archive', $reward) }}" method="POST" style="display: inline;">
                          @csrf @method('PATCH')
                          <div class="loginpage-btn btn-compact">
                            <button type="submit" style="background-color: #ffc107;" title="Archive reward"
                                    onclick="return confirm('Are you sure you want to archive this reward?')">
                              <i class="fas fa-archive"></i>
                            </button>
                          </div>
                        </form>
                      </div>
                    </div>
                  @endforeach
                </div>
              @endif

              <!-- Archived Rewards Section -->
              <div id="archivedRewardsSection" style="display: none; margin-top: 2rem; padding-top: 1rem; border-top: 2px solid #ddd;">
                <div class="text section-title">Archived Rewards</div>
                
                @if($archivedRewards && $archivedRewards->isEmpty())
                  <p style="text-align: center; color: #999;">No archived rewards found.</p>
                @else
                  <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @foreach($archivedRewards ?? [] as $reward)
                      <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: #f8f9fa; border-radius: 8px; border: 1px solid #dee2e6; opacity: 0.7;">
                        <div style="display: flex; align-items: center; gap: 1rem; flex: 1;">
                          <img src="{{ $reward->card_image ? asset($reward->card_image) : asset('images/giftcards/placeholder.png') }}" 
                              alt="{{ $reward->card_name }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                          <div>
                            <h4 style="margin: 0 0 0.25rem 0; font-size: 1.1em;">{{ $reward->card_name }} <span style="color: #999;">(Archived)</span></h4>
                            <p style="color: #666; font-size: 0.9em; margin: 0.25rem 0;">{{ Str::limit($reward->card_description ?? '', 50) }}</p>
                            <p style="color: #007bff; font-weight: bold; margin: 0.25rem 0;">{{ $reward->points_amount }} Points</p>
                          </div>
                        </div>

                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                          <form action="{{ route('admin.rewards.unarchive', $reward) }}" method="POST" style="display: inline;">
                            @csrf @method('PATCH')
                            <div class="loginpage-btn btn-compact">
                              <button type="submit" style="background-color: #28a745;" title="Unarchive reward"
                                      onclick="return confirm('Are you sure you want to unarchive this reward?')">
                                <i class="fas fa-undo"></i>
                              </button>
                            </div>
                          </form>

                          <form action="{{ route('admin.rewards.destroy', $reward) }}" method="POST" style="display: inline;">
                            @csrf @method('DELETE')
                            <div class="loginpage-btn btn-compact">
                              <button type="submit" style="background-color: #dc3545;" title="Delete reward"
                                      onclick="return confirm('Are you sure you want to delete this reward? This cannot be undone.')">
                                <i class="fas fa-trash"></i>
                              </button>
                            </div>
                          </form>
                        </div>
                      </div>
                    @endforeach
                  </div>
                @endif
              </div>
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection