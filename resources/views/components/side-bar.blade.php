<aside class="main-sidebar sidebar-light-primary elevation-4">
  <!-- Brand Logo -->
  <a href="#" class="brand-link">
    <img src="{{ asset('user.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-1" style="opacity: .8">
    <div class="brand-text font-weight-light"><strong>SEO</strong> <small>Catalog</small></div>
  </a>

  <div class="sidebar">
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="{{ Auth::user()->image ? asset("storage/avatar/".Auth::user()->username) : asset('user.png') }}" class="img-circle elevation-2" alt="User Image">
      </div>
      <div class="info">
        <a href="{{ route('users.profile.index', \Illuminate\Support\Facades\Auth::user()->username) }}" class="d-block text-wrap">{{ \Illuminate\Support\Facades\Auth::user()->username }}</a>
      </div>
    </div>

    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
          <a href="{{ route('dashboard.index') }}" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="nav-icon fa fa-home"></i>
            <p>
              Home
            </p>
          </a>
        </li>

        {{-- PROJECT TREE MENU --}}
        <li class="nav-item has-treeview {{ request()->is(['project', 'project/*', 'issue/*']) ? 'menu-open' : '' }}">
          <a href="#" class="nav-link {{ request()->is(['project', 'project/*', 'issue/*']) ? 'active' : '' }}">
            <i class="nav-icon fa fa-archive"></i>
            <p>
              Project
            </p>
            <i class="right fas fa-angle-left"></i>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('project.index') }}" class="nav-link {{ request()->is(['project', 'project/edit/*', 'issue/*']) ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Index</p>
                @if($project)
                  <i class="float-right badge bg-primary">{{ $project }}</i>
                @endif
              </a>
            </li>
            @if(Auth::user()->role != 4)
              <li class="nav-item">
                <a href="{{ route('project.create') }}" class="nav-link {{ request()->is('project/create') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Create</p>
                </a>
              </li>
            @endif
            @if ($unmanaged_project)
              <li class="nav-item">
                <a href="{{ route('project.unmanaged') }}" class="nav-link {{ request()->is('project/unmanaged') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Unmanaged</p>
                  <i class="float-right badge bg-warning">{{ $unmanaged_project }}</i>
                </a>
              </li>
            @endif
          </ul>
        </li>

        {{-- USER TREE MENU --}}
        @if(Auth::user()->role != 4)
          <li class="nav-item has-treeview {{ request()->is(['users', 'users/*']) ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->is(['users', 'users/*']) ? 'active' : '' }}">
              <i class="fas fa-users nav-icon"></i>
              <p>
                Users
              </p>
              <i class="right fas fa-angle-left"></i>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('users.index') }}" class="nav-link {{ request()->is(['users', 'users/edit']) ? 'active' : '' }}">
                  <i class="fas fa-users-cog nav-icon"></i>
                  <p>Index</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('users.create') }}" class="nav-link {{ request()->is('users/create') ? 'active' : '' }}">
                  <i class="fas fa-user-plus nav-icon"></i>
                  <p>Create</p>
                </a>
              </li>
            </ul>
          </li>
        @endif

        <li class="nav-item">
          <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="nav-link">
            <i class="nav-icon fas fa-power-off"></i>
            <p>
              Logout
            </p>
          </a>
        </li>
      </ul>
    </nav>
  </div>
</aside>
