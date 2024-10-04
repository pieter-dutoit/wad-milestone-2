<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ url('/enrolments') }}">
            <img src="{{ asset('images/logo.svg') }}" alt="" width="40" height="40">
        </a>
        <a class="navbar-brand" href="{{ url('/enrolments') }}">
            PeerPivot
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="{{ url('/enrolments') }}">Home</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Courses
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        @forelse (Auth::user()->courses->unique() as $course)
                            <li><a class="dropdown-item"
                                    href="{{ url('courses', [$course->id]) }}">{{ $course->course_name }}</a>
                            </li>
                        @empty
                            <li>No items to display</li>
                        @endforelse
                    </ul>
                </li>

                @if (Auth::user()->role->role == 'teacher')
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="{{ url('/uploads/create') }}">Bulk Enrolment</a>
                    </li>
                @endif

                <li></li>

            </ul>

            @auth
                <span class="navbar-text me-3">
                    Logged in as <strong>{{ Auth::user()->name }}</strong>
                    (<em>{{ Auth::user()->role->role }}</em>)
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-dark">Log out</button>
                </form>
            @endauth
        </div>
    </div>

</nav>
