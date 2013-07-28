Edit

{{ Form::model($model, array('action' => array($controller . '@update', $model->id), 'method' => 'PUT')) }}
{{ Form::renderFields($form, $errors) }}

{{ Form::submit('Save') }}
{{ Form::close() }}

<div>
    <a href="{{ URL::action($controller . '@index') }}">To overview</a>
</div>