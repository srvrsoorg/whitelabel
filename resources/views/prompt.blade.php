@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="rounded shadow bg-white overflow-hidden px-6 py-6">
            <h5 class="text-xl font-semibold text-gray-700">Horizon</h5>
            <p class="text-gray-500 mt-2 mb-5">Monitoring Queue and Jobs</p>
            <a href="/whitelabel/horizon" class="px-6 py-3 text-center text-white bg-indigo-500 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 font-medium rounded-lg">
                Go to Horizon
            </a>
      </div>
  
      <div class="rounded shadow bg-white overflow-hidden px-6 py-6">
            <h5 class="text-xl font-semibold text-gray-700">Telescope</h5>
            <p class="text-gray-500 mt-2 mb-5">Debug your applications with Telescope.</p>
            <a href="/whitelabel/telescope" class="px-6 py-3 text-center text-white bg-indigo-500 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 font-medium rounded-lg">
                Go to Telescope
            </a>
      </div>
    </div>
  </div>
@endsection
