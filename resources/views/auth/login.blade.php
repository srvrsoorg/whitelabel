@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-center items-center">
      <div class="w-full max-w-md bg-white rounded shadow px-6 py-8">
        <h1 class="text-2xl font-semibold text-gray-700 mb-4">Login</h1>
  
        <form method="POST" action="{{ route('login') }}">
          @csrf
  
          <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
            <input id="email" type="email" class="w-full px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
  
            @error('email')
                <span class="block text-red-500 text-xs font-medium mt-2">{{ $message }}</span>
            @enderror
          </div>
  
          <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
            <input id="password" type="password" class="w-full px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" name="password" required autocomplete="current-password">
  
            @error('password')
                <span class="block text-red-500 text-xs font-medium mt-2">{{ $message }}</span>
            @enderror
          </div>
  
          <div class="flex items-center mb-4">
            <input id="remember" type="checkbox" class="rounded h-5 w-5 text-indigo-600 border-gray-300 focus:ring-indigo-500 focus:ring-2" name="remember" {{ old('remember') ? 'checked' : '' }}>
            <label for="remember" class="ml-2 text-sm font-medium text-gray-700">Remember Me</label>
          </div>
  
          <div class="flex items-center justify-between">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-500 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 font-medium">
              Login
            </button>
  
            @if (Route::has('password.request'))
                <a class="text-sm text-gray-600 hover:text-gray-700 underline" href="{{ route('password.request') }}">
                    Forgot Your Password?
                </a>
            @endif
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
