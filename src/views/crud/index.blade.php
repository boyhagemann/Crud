Overview

<table>
	<thead>
	@foreach($overview->labels() as $label)
	<td>{{ $label }}</td>
	@endforeach
	</thead>
	<tbody>
	@foreach($overview->rows() as $id => $row)
	<tr>
		@foreach($row->columns() as $column)
		<td>{{ $column }}</td>
		@endforeach
		<td><a href="{{ URL::route($route . '.edit', $id) }}">Edit</a></td>
		<td>
			{{ Form::open(array('route' => array($route . '.destroy', $id), 'method' => 'DELETE')) }}
			{{ Form::submit('Delete') }}
			{{ Form::close() }}
		</td>
	</tr>
	@endforeach
	</tbody>
</table>

<div>
	<a href="{{ URL::route($route . '.create') }}">Create</a>
</div>