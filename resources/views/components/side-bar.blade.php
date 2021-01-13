<aside class="main-sidebar sidebar-light-primary elevation-4">
  <!-- Brand Logo -->
  <a href="#" class="brand-link">
    <img src="{{ asset('logo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-1" style="opacity: .8">
    <div class="brand-text font-weight-light"><strong>SEO</strong> <small>Catalog</small></div>
  </a>

  <div class="sidebar">
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="{{ asset('logo.png') }}" class="img-circle elevation-2" alt="User Image">
      </div>
      <div class="info">
        <a href="#" class="d-block text-wrap">{{ \Illuminate\Support\Facades\Auth::user()->username }}</a>
      </div>
    </div>

    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
          data-accordion="false">
        <li class="nav-item">
          <a href="{{ route('dashboard.index') }}" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="nav-icon fa fa-home"></i>
            <p>
              Home
            </p>
          </a>
        </li>

        {{--user--}}
        <li class="nav-item">
          <a href="{{ route('users.index') }}"
             class="nav-link {{ request()->is(['users', 'users/*']) ? 'active' : '' }}">
            <i class="nav-icon fa fa-users"></i>
            <p>
              users
            </p>
          </a>
        </li>

        {{--dice--}}
        <li class="nav-item">
          <a href="{{ route('dice.index') }}"
             class="nav-link {{ request()->is(['dice', 'dice/*']) ? 'active' : '' }}">
            <i class="nav-icon fa fa-dice"></i>
            <p>
              List user in dice
            </p>
          </a>
        </li>

        {{--binary--}}
        <li class="nav-item">
          <a href="{{ route('binary.index') }}" class="nav-link {{ request()->is('binary') ? 'active' : '' }}">
            <i class="nav-icon fa fa-network-wired"></i>
            <p>
              Network
            </p>
          </a>
        </li>

        {{--History--}}
        <li class="nav-item has-treeview {{ request()->is(['stats', 'stats/*']) ? 'menu-open' : '' }}">
          <a href="#" class="nav-link {{ request()->is(['stats', 'stats/*']) ? 'active' : '' }}">
            <i class="nav-icon fas fa-scroll"></i>
            <p>
              History
            </p>
            <i class="right fas fa-angle-left"></i>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('stats', ['turnover']) }}" class="nav-link {{ request()->is(['stats/turnover', 'stats/turnover/*']) ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>turnover</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('stats', ['turnover-today']) }}"
                 class="nav-link {{ request()->is(['stats/turnover-today', 'stats/turnover-today/*']) ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>turnover today</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('stats', ['upgrades-with-dividend']) }}"
                 class="nav-link {{ request()->is(['stats/upgrades-with-dividend']) ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>upgrades with dividend</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('stats', ['random-share']) }}"
                 class="nav-link {{ request()->is(['stats/random-share', 'stats/random-share/*']) ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>random share</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('stats', ['random-share-claimed']) }}"
                 class="nav-link {{ request()->is(['stats/random-share-claimed', 'stats/random-share-claimed/*']) ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>random share claimed</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('stats', ['random-share-unclaimed']) }}"
                 class="nav-link {{ request()->is(['stats/random-share-unclaimed', 'stats/random-share-unclaimed/*']) ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>random share unclaimed</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('stats', ['new-member']) }}"
                 class="nav-link {{ request()->is(['stats/new-member', 'stats/new-member/*']) ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>new member</p>
              </a>
            </li>
          </ul>
        </li>

        {{--List Queue--}}
        <li class="nav-item has-treeview {{ request()->is(['queue', 'queue/*']) ? 'menu-open' : '' }}">
          <a href="#" class="nav-link {{ request()->is(['queue', 'queue/*']) ? 'active' : '' }}">
            <i class="nav-icon fas fa-spinner"></i>
            <p>
              Queue
            </p>
            <i class="right fas fa-angle-left"></i>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('queue.index') }}" class="nav-link {{ request()->is(['queue']) ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Index</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('queue.share.index') }}"
                 class="nav-link {{ request()->is(['queue/share/*']) ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Share</p>
              </a>
            </li>
          </ul>
        </li>

        {{-- Settings --}}
        <li class="nav-item has-treeview {{ request()->is(['setting', 'setting/*']) ? 'menu-open' : '' }}">
          <a href="#" class="nav-link {{ request()->is(['setting', 'setting/*']) ? 'active' : '' }}">
            <i class="nav-icon fas fa-cogs"></i>
            <p>
              Settings
            </p>
            <i class="right fas fa-angle-left"></i>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('setting.camel.index') }}"
                 class="nav-link {{ request()->is(['setting/camel']) ? 'active' : '' }}">
                <i class="fas fa-coins nav-icon"></i>
                <p>BANK Settings</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('setting.upgrade-list.index') }}"
                 class="nav-link {{ request()->is(['setting/upgrade-list']) ? 'active' : '' }}">
                <i class="fas fa-money-bill-wave nav-icon"></i>
                <p>Upgrades Setting</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('setting.share-level.index') }}"
                 class="nav-link {{ request()->is(['setting/share-level']) ? 'active' : '' }}">
                <i class="fas fa-cubes nav-icon"></i>
                <p>Share Level Settings</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('setting.wallet-admin.index') }}"
                 class="nav-link {{ request()->is(['setting/wallet-admin']) ? 'active' : '' }}">
                <i class="fas fa-wallet nav-icon"></i>
                <p>Wallet Admin</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('setting.advanced.index') }}"
                 class="nav-link {{ request()->is(['setting/advanced']) ? 'active' : '' }}">
                <i class="fas fa-tools nav-icon"></i>
                <p>Advanced Settings</p>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-item">
          <a href="{{ route('logout') }}"
             onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="nav-link">
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
