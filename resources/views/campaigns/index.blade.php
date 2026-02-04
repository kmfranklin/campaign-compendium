@extends('layouts.app')

@section('content')
<div class="w-full">
    <div class="sm:flex sm:items-center sm:justify-between py-6">
        <h1 class="text-2xl font-semibold text-text">Campaigns</h1>

        <a href="{{ route('campaigns.create') }}"
           class="inline-flex items-center px-4 py-2 bg-accent hover:bg-accent-hover text-on-accent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-accent">
            + New Campaign
        </a>
    </div>

    {{-- Results Table + Mobile Cards --}}
    @include('campaigns.partials.results', ['campaigns' => $campaigns])
</div>
@endsection
