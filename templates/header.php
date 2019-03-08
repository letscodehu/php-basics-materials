<nav class="navbar navbar-expand-1g navbar-dark bg-dark">
    <a class="navbar-brand" href="/">My Photos</a>
    <ul class="navbar-nav pull-right">
        <li class="nav-item active">
            <?php if($loggedIn): ?>
            <a class="nav-link" href="/logout">Logout</a>
            <?php else: ?>
            <a class="nav-link" href="/login">Login</a>
            <?php endif ?>
        </li>
    </ul>
</nav>