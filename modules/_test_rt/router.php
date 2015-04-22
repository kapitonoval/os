<?php

    // ** БЕЗОПАСНОСТЬ **
	// проверяем выдан ли доступ на вход на эту страницу
	// если нет $ACCESS['suppliers']['access'] или она равна FALSE прерываем работу скирпта 
	if(!@$ACCESS['_test_rt']['access']) exit($ACCESS_NOTICE);
	// ** БЕЗОПАСНОСТЬ **
	
	
	// Данные расчетной таблицы хронятся в 3-х таблицах базы данных
	// 1-я таблица является родительской для 2-ой , 2-ая родительской для 3-ей
	// os__rt_main_rows (RT_MAIN_ROWS)  1-я таблица основные ряды таблицы, содержащие описательные данные о товарной позиции (наименование, тип )
	// os__rt_dop_data (RT_DOP_DATA)  2-я таблица содрежит данные о количестве и цене для каждого варианта расчета товарной позиции (к каждой строке из 1-ой таблицы может соответсвовать любое количество стро из 2-ой таблицы)
	// os__rt_print_data (RT_PRINT_DATA) 3-я таблица содержит данные о расчетах нанесения (к каждой строке из 2-ой таблицы может соответсвовать любое количество стро из 3-ей таблицы)
	
	//  cat catalogs - товары из каталогов 
	//  pol polygraphy - полиграфия 
	//  ext externals (внешние) - товары не из каталогов 

	function fetch_rows_from_rt($order_num){
	     global $mysqli;
		 
		 $rows = array();
		 
		 $query = "SELECT main_tbl.id AS main_id ,main_tbl.type AS main_row_type  ,main_tbl.art AS art ,main_tbl.name AS item_name ,
		 
		                  dop_data_tbl.id AS dop_data_id , dop_data_tbl.row_id AS dop_t_row_id , dop_data_tbl.quantity AS dop_t_quantity , dop_data_tbl.in_price AS dop_t_in_price , dop_data_tbl.out_price AS dop_t_out_price , dop_data_tbl.discount AS dop_t_discount ,
						  
						  print_data_tbl.id AS print_id , print_data_tbl.dop_row_id AS print_t_dop_row_id ,print_data_tbl.type AS print_t_type ,
		                  print_data_tbl.quantity AS print_t_quantity ,print_data_tbl.price AS print_t_price 
		          FROM 
		          `".RT_MAIN_ROWS."`  main_tbl 
				  LEFT JOIN 
				  `".RT_DOP_DATA."`   dop_data_tbl   ON  main_tbl.id = dop_data_tbl.row_id
				  LEFT JOIN 
				  `".RT_PRINT_DATA."` print_data_tbl ON  dop_data_tbl.id = print_data_tbl.dop_row_id
		          WHERE main_tbl.order_num ='".$order_num."' ORDER BY main_tbl.id";
				  
		 $result = $mysqli->query($query) or die($mysqli->error);
		 $multi_dim_arr = array();
	     while($row = $result->fetch_assoc()){
		     if(!isset($multi_dim_arr[$row['main_id']])){
			     $multi_dim_arr[$row['main_id']]['row_type'] = $row['main_row_type'];
				 $multi_dim_arr[$row['main_id']]['art'] = $row['art'];
				 $multi_dim_arr[$row['main_id']]['name'] = $row['item_name'];
			 }
			 //$multi_dim_arr[$row['main_id']]['print_id'][] = $row['print_id'];
			 if(isset($multi_dim_arr[$row['main_id']]) && !isset($multi_dim_arr[$row['main_id']]['dop_data'][$row['dop_data_id']]) &&!empty($row['dop_data_id'])){
			     $multi_dim_arr[$row['main_id']]['dop_data'][$row['dop_data_id']] = array(
																	'quantity' => $row['dop_t_quantity'],
																	'in_price' => $row['dop_t_in_price'],
																	'out_price' => $row['dop_t_out_price']);
		    }
			if(isset($multi_dim_arr[$row['main_id']]['dop_data'][$row['dop_data_id']]) && !empty($row['print_id'])){
			    $multi_dim_arr[$row['main_id']]['dop_data'][$row['dop_data_id']]['print_data'][$row['print_id']] = array(
																									'type' => $row['print_t_type'],
																									'quantity' => $row['print_t_quantity'],
																									'price' => $row['print_t_price'],
																									'print_id' => $row['print_id']
																									);
			}
			
			//print_r( $multi_dim_arr[$row['main_id']]['dop_data'][$row['dop_data_id']]['print_data']); echo "<br>";
			
			$rows[]= '<tr><td>'.implode('</td><td>',$row).'</td></tr>';
		   
		 }
	     return array($multi_dim_arr,$rows);
	 }
	 
	 $rows = fetch_rows_from_rt(10147);
	 
	 foreach($rows[0] as $key => $row){
	     $dop_rows = array();
		 // если товарная позиция имеет больше одного варианта расчета добавляем пустой ряд в начало строки
		 if(count($row['dop_data'])>1) array_unshift($row['dop_data'],array('quantity'=>0,'in_price'=>0,'out_price'=>0,'print_data'=>0));
		 $row_span = count($row['dop_data']);
		 $counter=0;
	     foreach($row['dop_data'] as $dop_key => $dop_row){
		     // если данных по печати нет выводи +(плюс), если есть проверям чтобы $dop_row['print_data'] был массивом, если это не массив значит 
			 // специально добавленное значение чтобы обозначить что это пустой первый ряд
		     $print_btn = !isset($dop_row['print_data']) ? 'печать +' : ( is_array($dop_row['print_data'])? 'печать '.count($dop_row['print_data']) : '' );
		     $cur_row  =  '';
		     $cur_row .=  '<tr>';
		     $cur_row .=  ($counter==0)? '<td rowspan="'.$row_span.'">'.$key.'</td>':'';
		     $cur_row .=  ($counter==0)? '<td rowspan="'.$row_span.'">'.$row['row_type'].'</td>':'';
			 $cur_row .=  ($counter==0)? '<td rowspan="'.$row_span.'">'.$row['art'].''.$row['name'].'</td>':'';
			 $cur_row .=  '<td>'.$dop_row['quantity'].'</td>
						   <td>'.$dop_row['in_price'].'</td>
						   <td>'.$dop_row['out_price'].'</td>
						   <td>'.$print_btn.'</td> 
					   </tr>';
		    $tbl_rows[]= $cur_row;
		    $counter++;
		     // rowspan=""$tbl_rows[] = '<div style="border:#000 solid 1px">'.$dop_row['quantity'].'  '.$dop_row['in_price'].'  <td>'.print_r($dop_row,TRUE).'</td>'$dop_row['out_price'].'</div>';
		
		
		 }/*
		 $dop_rows_num = count($row['dop_data']);
	     $cur_row  =  '<tr>';
		 $cur_row .=  '<td rowspan="">'.$key.'</td>';
		 $cur_row .=  '<td>'.$row['row_type'].'</td>
						  <td>'.$row['art'].'</td>
						  <td>'.$row['name'].'</td>
						  <td>'.implode('',$dop_rows).'</td>
						  <td>'.$row[''].'</td>
					   </tr>';
		 $tbl_rows[]= */
	 
	 }
	 
	 echo '<table border="1">'.implode('',$tbl_rows).'</table>';
	 echo '<pre>'; print_r($rows[0]); echo '<pre>';
	
	
?>