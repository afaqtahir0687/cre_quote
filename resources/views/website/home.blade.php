@extends('website.layouts.app')

@section('content')
    {{-- Dynamically render active sections in their CMS database-configured sort order --}}
    @foreach($orderedSections as $sec)
        @if($sec->is_active && view()->exists("website.sections.{$sec->section_key}"))
            @include("website.sections.{$sec->section_key}")
        @endif
    @endforeach
@endsection
