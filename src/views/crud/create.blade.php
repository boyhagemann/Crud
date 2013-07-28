Create

{{ Form::model($model, array('action' => $controller . '@store')) }}
{{ Form::renderFields($form, $errors) }}

{{ Form::submit('Save') }}
{{ Form::close() }}

<div>
    <a href="{{ URL::action($controller . '@index') }}">To overview</a>
</div>