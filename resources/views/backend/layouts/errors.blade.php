@if (isset($errors) && count($errors) > 0)
    @foreach ($errors->all() as $error)
    <div class="alert alert-danger custom ">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
        {{ $error }}<br />
    </div>
    @endforeach
@endif

@if (Session::has('error'))
<div class="alert alert-danger">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
    {!! Session::pull('error') !!}
</div>
@endif

@if (Session::has('success'))
<div class="alert alert-success ">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
    {!! Session::pull('success') !!}
</div>
@endif
