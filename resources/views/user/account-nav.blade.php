<ul class="account-nav">
    <li><a href={{ route('user.index') }} class="menu-link menu-link_us-s">Panel</a></li>
    <li><a href="{{ route('user.orders') }}" class="menu-link menu-link_us-s">Órdenes</a></li>
    <li><a href="{{ route('wishlist.index') }}" class="menu-link menu-link_us-s">Lista de Deseos</a></li>

    <li>
        <form method="POST" action="{{ route('logout') }}" id="logout-form">
            @csrf
            <a href="{{ route('logout') }}" class=""
                onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                <div class="text">Salir</div>
            </a>
        </form>
    </li>
</ul>
