<nav>
    <p><em>Peer Reviews</em></p>
    <ul>
        @auth
            <li>Logged in as <strong>{{ Auth::user()->name }}</strong></li>
            <li><em>{{ Auth::user()->role->role }}</em></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Log out</button>
                </form>
            </li>
        @endauth
    </ul>
</nav>
