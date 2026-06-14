@inject('frontendAssets', 'App\Support\FrontendAssets')
@php
    $cssUrl = $frontendAssets->cssUrl();
    $jsUrl = $frontendAssets->jsUrl();
@endphp
@if($cssUrl)
    <link rel="stylesheet" href="{{ $cssUrl }}">
@endif
@if($jsUrl)
    <script type="module" src="{{ $jsUrl }}"></script>
@endif
