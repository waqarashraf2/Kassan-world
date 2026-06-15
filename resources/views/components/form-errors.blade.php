@if($errors->any())
    <div class="form-alert error" role="alert">
        <strong>Please check the highlighted information.</strong>
        <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif
