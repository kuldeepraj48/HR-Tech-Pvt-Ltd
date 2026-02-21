<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'URL Shortener')</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 5px; }
        nav { background: #333; padding: 12px 20px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
        nav .nav-left { display: flex; align-items: center; gap: 20px; }
        nav .nav-right { display: flex; align-items: center; }
        nav a { color: white; text-decoration: none; }
        nav a:hover { text-decoration: underline; }
        .page-header { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 12px; }
        .page-header h1 { margin: 0; font-size: 1.5rem; font-weight: 600; text-align: left; }
        .page-header .header-actions { margin-left: auto; text-align: right; }
        .form-actions { margin-top: 16px; text-align: left; }
        .form-actions .btn { margin-right: 8px; }
        .table-actions { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin: 16px 0; }
        .table-actions .left { text-align: left; }
        .table-actions .right { text-align: right; margin-left: auto; }
        .pagination-wrap { margin-top: 20px; text-align: center; }
        .pagination-wrap .pagination-info { margin-bottom: 10px; text-align: center; }
        .pagination-wrap .pagination-links { display: flex; justify-content: center; gap: 8px; flex-wrap: wrap; }
        .alert { padding: 10px; margin-bottom: 20px; border-radius: 5px; text-align: left; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error { background: #f8d7da; color: #721c24; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
        .btn { padding: 8px 16px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; text-align: center; }
        .btn:hover { background: #0056b3; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        form { margin: 20px 0; text-align: left; max-width: 480px; }
        input[type="text"], input[type="email"], input[type="password"], input[type="url"], select { width: 100%; padding: 8px; margin: 5px 0; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        label { display: block; margin-top: 10px; text-align: left; }
    </style>
</head>
<body>
    @auth
    <nav>
        <div class="nav-left">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            @if(Auth::user()->hasRole('SuperAdmin'))
                <a href="{{ route('clients.index') }}">Clients</a>
            @elseif(Auth::user()->hasRole('Admin'))
                <a href="{{ route('team-members.index') }}">Team Members</a>
            @endif
            <a href="{{ route('short-urls.index') }}">Short URLs</a>
        </div>
        <div class="nav-right">
            <form method="POST" action="{{ route('logout') }}" style="display: inline; margin: 0;">
                @csrf
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </div>
    </nav>
    @endauth

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>

