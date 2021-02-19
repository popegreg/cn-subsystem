<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		.tg  {border-collapse:collapse;border-spacing:0;border-color:#999;}
		.tg td{font-family:Arial, sans-serif;font-size:11px;padding:11px 20px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#999;color:#444;background-color:#F7FDFA;}
		.tg th{font-family:Arial, sans-serif;font-size:11px;font-weight:normal;padding:11px 20px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#999;color:#fff;background-color:#26ADE4;}
		.tg .tg-amwm{font-weight:bold;text-align:center;vertical-align:top}
		.tg .tg-yw4l{vertical-align:top}
		.tg .tg-6k2t{background-color:#D2E4FC;vertical-align:top}
		.tg .tg-f3zi{background-color:#D2E4FC;font-size:11px;vertical-align:top}
		.title {color:#36343c;}
	</style>
</head>
<body>
	<h3 class="title">CN Items that will expire within a month</h3>
	<table class="tg">
		<tr>
			<th class="tg-amwm">Item</th>
			<th class="tg-amwm">Description</th>
			<th class="tg-amwm">Quantity<br></th>
			<th class="tg-amwm">Lot No<br></th>
			<th class="tg-amwm">Location<br></th>
			<th class="tg-amwm">Date Received<br></th>
			<th class="tg-amwm">Expiration Date<br></th>
		</tr>
		@foreach ($data as $key => $inv)
			<tr>
				<td class="tg-6k2t">{{ $inv['item'] }}</td>
				<td class="tg-6k2t">{{ $inv['item_desc'] }}</td>
				<td class="tg-6k2t">{{ $inv['qty'] }}</td>
				<td class="tg-6k2t">{{ $inv['lot_no'] }}</td>
				<td class="tg-6k2t">{{ $inv['location'] }}</td>
				<td class="tg-6k2t">{{ $inv['received_date'] }}</td>
				<td class="tg-6k2t">{{ $inv['exp_date'] }}</td>
			</tr>
		@endforeach
	</table>

</body>
</html>

