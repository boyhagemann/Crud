<div class="page-header">
	<h1>Manage resource</h1>
</div>

{{ Form::render($form) }}

<a href="{{ URL::action('Boyhagemann\Crud\ManagerController@index') }}">View used controllers</a>