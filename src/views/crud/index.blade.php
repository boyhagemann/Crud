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
			<td><a href="{{ URL::action($class . '@edit', $id) }}">Edit</a></td>
			<td>
				{{ Form::open(array('action' => array($class . '@destroy', $id), 'method' => 'DELETE')) }}
				{{ Form::submit('Delete') }}
				{{ Form::close() }}
			</td>
		</tr>
	@endforeach
	</tbody>
</table>

<div>
	<a href="{{ URL::action($class . '@create') }}">Create</a>
</div>