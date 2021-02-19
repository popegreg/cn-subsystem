<h3>Part Code: 
@foreach($data as $value)
	{{ $value['code'] }}
@endforeach
</h3>
<h3>Part Name: 
@foreach ($po_details as $povalue)
	{{ $povalue->Name }}
@endforeach
</h3>
<h3>Customer: {{ $povalue->Customer }}</h3>
<table border="1" cellpadding="0" cellspacing="0" style="width: 500px;">
	<thead>
		<tr>
			<th scope="col">R3Answer</th>
			<th scope="col">Qty</th>
			<th scope="col">Time</th>
			<th scope="col">Re</th>
		</tr>
	</thead>
	<tbody>
		@foreach($answers as $answer)
		<tr>
			<td>{{ $answer->r3answer }}</td>
			<td style="text-align: right">{{ $answer->qty }}</td>
			<td style="text-align: right">{{ $answer->time }}</td>
			<td>{{ $answer->re }}</td>
		</tr>
		@endforeach
	</tbody>
</table>
<p>NEW: &nbsp; {{ $value['new1'] }} &nbsp; {{ $value['new2'] }}</p>
<p>WHAT IS THE REASON:&nbsp; {{ $value['reason'] }}</p>
<p>NOTE:&nbsp; {{ $value['note'] }}</p>