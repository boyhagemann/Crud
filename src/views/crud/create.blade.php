Create

{{ Form::model($model, array('route' => $route . '.store')) }}
{{ Form::renderFields($form, $errors) }}

{{ Form::submit('Save') }}
{{ Form::close() }}

<div>
    <a href="{{ URL::route($route . '.index') }}">To overview</a>
</div>