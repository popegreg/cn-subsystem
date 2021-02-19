<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title></title>
	<style type="text/css">
		.leftAlign {
		    text-align: left;
		    width:16.66%;
		}
		body{
			margin:0 auto;
		}
		td{
			text-align: center;
			height: 30;
		}
		th{
			text-align: center;
			height: 30;
		}
		.leftAlignColspan2 {
		    text-align: left;
		    width:33.33%;
		}
		.tg  {border-collapse:collapse;border-spacing:0;}
        .tg td{font-family:Arial, sans-serif;font-size:15px;padding:10px 5px;border-style:solid;border-width:2px;overflow:hidden;word-break:normal;}
        .tg th{font-family:Arial, sans-serif;font-size:15px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
        .tg .tg-7uzy{vertical-align:top; font-weight: 900; height: 18px;}
        .tg .tg-yw4l{vertical-align:top; font-weight: 900; height: 18px;}
        .tg-wrap {width: 100%}
        thead{display: table-header-group;}
		tfoot {display: table-row-group;}
		tr {page-break-inside: avoid;}
	</style>
</head>
<body>
	
	<?php
		$output = "";
        $color = '';
        $totalusage = '';
        $cnt = 0;
        $sup = '';

		foreach($bomcontent as $line)
		{
			$d = explode("\t", $line);

            if ($d[2] == '2') {
                $color = '#01a7e1';
            } else {
                $color = '';
            }

            if ($d[10] == 'Y016001') {
            	$sup = "PPD";
            } else {
            	//$sup = $supplier[$cnt];
            	$sup = "YEC";
            }

            $basic_qty = preg_replace('/[,]/', '', $d[14]);

            $basic_usage = preg_replace('/[,]/', '', $d[6]);
            $usage = $basic_usage / $basic_qty;

        	$totalusage = $usage * $orderqty;
            $output = $output.'<tr class="line-height" style="background-color: '.$color.'">
								    <td class="tg-7uzy">'.$d[2].'</td>
								    <td class="tg-7uzy">'.$d[5].'</td>		
								    <td class="tg-7uzy">'.mb_convert_encoding($d[4],"UTF-8","SJIS").'</td>
								    <td class="tg-7uzy">'.mb_convert_encoding($d[9],"UTF-8","SJIS").'</td>
								    <td class="tg-7uzy">'.$d[7].'</td>		
								    <td class="tg-7uzy">'.$usage.'</td> 
								    <td class="tg-7uzy">'.$totalusage.'</td>
								    <td class="tg-7uzy">'.$sup.'</td>		
								    <td class="tg-7uzy"></td>
								</tr>'; //number_format($d[6],4)
				
            
            $cnt++;
		}
		$bomdata = '<table class="tg" style="width:100%; font-size: 15px;" border="1" cellspacing="0"  cellpadding="0" >
						<thead style="margin-top:10px;">
							<tr>
							    <th class="tg-yw4l">LV</th>
							    <th class="tg-yw4l">PARTS CODE</th>		
							    <th class="tg-yw4l">PARTS NAME</th>
							    <th class="tg-yw4l">DRAWING NO.</th>
							    <th class="tg-yw4l">UNIT</th>		
							    <th class="tg-yw4l">USAGE</th>
							    <th class="tg-yw4l">TOTAL USAGE</th>
							    <th class="tg-yw4l">SUPPLIER</th>		
							    <th class="tg-yw4l">SAKI</th>
						  	</tr>
						 </thead>
						<tbody>'.$output.
						'</tbody>
					</table>';

		//echo $header;
		echo $bomdata;
	?>
</body>
</html>