<ul class="account-nav">
    <li><a href={{ route('user.index') }} class="menu-link menu-link_us-s">Dashboard</a></li>
<<<<<<< HEAD
    <li><a href="{{ route('user.orders') }}" class="menu-link menu-link_us-s">Órdenes</a></li>
    <li><a href="account-address.html" class="menu-link menu-link_us-s">Direcciones</a></li>
    <li><a href="account-details.html" class="menu-link menu-link_us-s">Detalles de la Cuenta</a></li>
    <li><a href="account-wishlist.html" class="menu-link menu-link_us-s">Lista de Deseos</a></li>
=======
    <li><a href="{{ route('user.orders') }}" class="menu-link menu-link_us-s">Orders</a></li>
    <li><a href="account-details.html" class="menu-link menu-link_us-s">Account Details</a></li>
    <li><a href="{{ route('wishlist.index') }}" class="menu-link menu-link_us-s">Wishlist</a></li>
>>>>>>> origin/main

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
