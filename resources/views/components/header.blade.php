<nav class="main-header navbar navbar-expand navbar-dark navbar-primary">
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
    </li>
  </ul>
  <ul class="navbar-nav ml-auto">
    @if($totalAlert)
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <span class="badge badge-dark navbar-badge">{{ $totalAlert }}</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <div class="dropdown-divider"></div>
          @if($newIssue)
            <a href="{{ route('project.show', 'issue') }}" class="dropdown-item">
              <i class="fas fa-envelope mr-2"></i> {{ $newIssue }} new Tasks
              @if($lastIssue->y)
                <span class="float-right text-muted text-sm">{{ $lastIssue->y }} years</span>
              @elseif($lastIssue->m)
                <span class="float-right text-muted text-sm">{{ $lastIssue->m }} months</span>
              @elseif($lastIssue->d)
                <span class="float-right text-muted text-sm">{{ $lastIssue->d }} days</span>
              @elseif($lastIssue->h)
                <span class="float-right text-muted text-sm">{{ $lastIssue->h }} hours</span>
              @elseif($lastIssue->i)
                <span class="float-right text-muted text-sm">{{ $lastIssue->i }} minutes</span>
              @elseif($lastIssue->s)
                <span class="float-right text-muted text-sm">{{ $lastIssue->s }} seconds</span>
              @endif
            </a>
          @endif
          <div class="dropdown-divider"></div>
          @if($newProject)
            <a href="{{ route('project.index') }}" class="dropdown-item">
              <i class="fas fa-archive mr-2"></i> {{ $newProject }} new Projects
              @if($lastProject->y)
                <span class="float-right text-muted text-sm">{{ $lastProject->y }} years</span>
              @elseif($lastProject->m)
                <span class="float-right text-muted text-sm">{{ $lastProject->m }} months</span>
              @elseif($lastProject->d)
                <span class="float-right text-muted text-sm">{{ $lastProject->d }} days</span>
              @elseif($lastProject->h)
                <span class="float-right text-muted text-sm">{{ $lastProject->h }} hours</span>
              @elseif($lastProject->i)
                <span class="float-right text-muted text-sm">{{ $lastProject->i }} minutes</span>
              @elseif($lastProject->s)
                <span class="float-right text-muted text-sm">{{ $lastProject->s }} seconds</span>
              @endif
            </a>
          @endif
          <div class="dropdown-divider"></div>
          @if($newTag)
            <a href="{{ route('project.show', 'tag') }}" class="dropdown-item">
              <i class="fas fa-file mr-2"></i> {{ $newTag }} new Tag
              @if($lastTag->y)
                <span class="float-right text-muted text-sm">{{ $lastTag->y }} years</span>
              @elseif($lastTag->m)
                <span class="float-right text-muted text-sm">{{ $lastTag->m }} months</span>
              @elseif($lastTag->d)
                <span class="float-right text-muted text-sm">{{ $lastTag->d }} days</span>
              @elseif($lastTag->h)
                <span class="float-right text-muted text-sm">{{ $lastTag->h }} hours</span>
              @elseif($lastTag->i)
                <span class="float-right text-muted text-sm">{{ $lastTag->i }} minutes</span>
              @elseif($lastTag->s)
                <span class="float-right text-muted text-sm">{{ $lastTag->s }} seconds</span>
              @endif
            </a>
          @endif
        </div>
      </li>
    @endif
    <li class="nav-item">
      <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();" role="button">
        <i class="nav-icon fas fa-power-off"></i>
      </a>
    </li>
  </ul>
</nav>