Create

{{ Form::model($model, array('action' => $action)) }}
{{ Form::renderFields($form, $errors) }}

{{ Form::submit('Save') }}
{{ Form::close() }}