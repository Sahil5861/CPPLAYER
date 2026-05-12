<!DOCTYPE html>
<html>
<head>
    <title>Account Frozen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-light">

    @php
        $admin_id = Auth::user()->created_by;

        $admin = \App\Models\User::where('id', $admin_id)->first();

    @endphp

    <div class="text-center">
        <h1 class="text-danger">403</h1>
        <h3>Account Frozen</h3>
        <p>Your account has been frozen. You cannot perform any actions.</p>
        <p class="text-center">Contact to Your admin : <a href="tel:{{$admin->mobile}}" class="text-dark">{{$admin->mobile}}</a></p>
        <a href="/logout" class="btn btn-primary">Logout</a>
    </div>

</body>
</html>