@extends('layouts.contentNavbarLayout')

@section('title', 'Menu Debug Test')

@section('content')
<div class="container">
    <h1>Menu Debug Test</h1>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    <div class="row mb-3">
        <div class="col">
            <div class="btn-group" role="group">
                <a href="{{ route('test.login', 'admin') }}" class="btn btn-primary">Login as Admin</a>
                <a href="{{ route('test.login', 'vendor') }}" class="btn btn-warning">Login as Vendor</a>
                <a href="{{ route('test.login', 'user') }}" class="btn btn-info">Login as User</a>
                <a href="{{ route('test.logout') }}" class="btn btn-secondary">Logout</a>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <h5>Authentication Status</h5>
            <ul>
                <li><strong>Authenticated:</strong> {{ $data['authenticated'] ? 'Yes' : 'No' }}</li>
                @if($data['authenticated'])
                    <li><strong>User Email:</strong> {{ $data['user_email'] }}</li>
                    <li><strong>Is Admin:</strong> {{ $data['is_admin'] ? 'Yes' : 'No' }}</li>
                    <li><strong>Is Vendor:</strong> {{ $data['is_vendor'] ? 'Yes' : 'No' }}</li>
                    <li><strong>Roles:</strong> {{ $data['roles']->join(', ') }}</li>
                @endif
            </ul>
            
            <h5>Menu Data</h5>
            @if(isset($menuData) && $menuData)
                <p><strong>Menu data is available.</strong></p>
                @if(isset($menuData[0]) && isset($menuData[0]->menu))
                    <p>First menu header: <strong>{{ $menuData[0]->menu[0]->menuHeader ?? 'No header found' }}</strong></p>
                    <p>Total menu items: <strong>{{ count($menuData[0]->menu) }}</strong></p>
                @else
                    <p>Menu structure is not as expected.</p>
                @endif
            @else
                <p><strong>No menu data available!</strong></p>
            @endif
            
            <h5>Current Route</h5>
            <p><strong>Route Name:</strong> {{ \Route::currentRouteName() }}</p>
            <p><strong>Route URI:</strong> {{ request()->path() }}</p>
        </div>
    </div>
</div>
@endsection
