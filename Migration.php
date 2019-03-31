<?php
$tempoinicial=microtime(true);
echo "<h4>in ". $tempoinicial. " Milli seconds </h4><br>";
//echo ($tempoinicial);
echo "hello,i don't bug out!! /br";
get_data();
$interval = microtime(true)- $tempoinicial;
$minutes = (int)($interval/60);
$seconds = (int)$interval-$minutes*60;
$milis = ($interval-(int)$interval)*1000;

echo "<h4>Em ". $minutes. "Minutes, ".$seconds." seconds, e " .$milis." Milli seconds </h4><br>";


 

function get_data(){
	
	$urlOrigem="127.0.0.1";
	$databaseOrigem="mydb27origem";
	$usernameOrigem="root";
	$passwordOrigem="";

	$urlDestino="127.0.0.1";
	$databaseDestino="mydb27destino";
	$usernameDestino="root";
	$passwordDestino="";
	
	$connOrigem = mysqli_connect($urlOrigem, $usernameOrigem, $passwordOrigem, $databaseOrigem);

	if (!$connOrigem){
		die ("Connection Failled: ".$connOrigem->connect_error);
	}
	else{
		echo"conecção origem";
	}
	
	$connDestino=mysqli_connect($urlDestino, $usernameDestino, $passwordDestino, $databaseDestino);
	
	if (!$connDestino){
		die ("Connection Failled: ".$connDestino->connect_error);
	}else{
		echo"conecção destino";
	}
	$migrados=0;
	$aux= $migrados+migrarTabela("investigador_log",$connOrigem,$connDestino);
	$migrados=$aux;
	$aux= $migrados+migrarTabela("medicao_log",$connOrigem,$connDestino);
	
	
	
	$migrados=$aux;
	$aux= $migrados+migrarTabela("medicaotemperatura_log",$connOrigem,$connDestino);
	
	$migrados=$aux;
	$aux= $migrados+migrarTabela("variavel_log",$connOrigem,$connDestino);
	
	$migrados=$aux;
	$aux= $migrados+migrarTabela("medicaoluminosidade_log",$connOrigem,$connDestino);
	
	$migrados=$aux;
	$aux= $migrados+migrarTabela("cultura_log",$connOrigem,$connDestino);
	
	$migrados=$aux;
	$aux= $migrados+migrarTabela("sistema_log",$connOrigem,$connDestino);
	echo $aux; echo"migrados";
	echo "<h4>Foram migrados ".$aux." Logs!!</h4><br>";
	
	mysqli_close ($connOrigem);
	mysqli_close ($connDestino);
	
	//return json_encode($rows);
}


function migrarTabela($tabela,$connOrigem,$connDestino){
	$id=getLastId($tabela,$connDestino);
	echo "this is my id:";
	echo  $id;
	$rows = getLog($connOrigem, $id, $tabela);
	$size=sizeof($rows);
	if ($size!=0){
		echo "rows not empty".$size;
		echo '<pre>'; print_r($rows); echo '</pre>';
			InsertintoLogs($connDestino,$rows,$tabela);
		}else{
			echo "rows empty (;_;)";
		}
	return $size;
}
  function qet_sql($whereclause, $tabela){
	 if ($tabela == "Medicao_log") {
		return	
			 "Select * from medicao_log where id > ".$whereclause ;
	}
	else if($tabela == "investigador_log") {
		return
			 "Select * from investigador_log where id > ".$whereclause ;
	}
	else if($tabela == "medicaotemperatura_log") {
		return	
			 "Select * from medicaotemperatura_log where id > ".$whereclause ;
	}
	else if($tabela =="variavel_log") {
		return	
			 "Select * from variavel_log where id > ".$whereclause ;
	}
	else if($tabela == "medicaoLuminosidade_log") {
		return
			 "Select * from medicaoluminosidade_log where id > ".$whereclause ;
	}
	else if($tabela == "cultura_log") {
		return
			 "Select * from cultura_log where id > ".$whereclause;
	}
	else if($tabela == "sistema_log") {
		return
			 "Select * from sistema_log where id > ".$whereclause ;
	}
	  
  }
function getLog($connOrigem, $whereclause, $tabela){
	echo "open getLog with table".$tabela;
	$sql = qet_sql($whereclause, $tabela);
	echo $sql;
	$result = mysqli_query($connOrigem, $sql);
	echo '<pre>'; print_r($result); echo '</pre>';
	$rows = array();
	if ($result) {
		echo "found log to migrate";
		if (mysqli_num_rows($result)>0){
			while($r=mysqli_fetch_assoc($result)){
				array_push($rows, $r);	
				echo '<pre>'; print_r($rows); echo '</pre>';

			}
		}
		

		return $rows;	
	}
}

function getLastId($tabela, $connDestino){
	$sql= "Select max(id) from ".$tabela;
	
	$result = mysqli_query($connDestino, $sql);
	echo $sql;
	
	echo '<pre>'; print_r($result); echo '</pre>';
	
	$row = mysql_fetch_array($result);
	echo '<pre>row:'; print_r($row); echo '</pre>';
	$id = 0;echo '<pre> goddammit'; print_r($id); echo '</pre>';
	$rows = array();
	
	if ($result) {
		echo "found id to migrate";
		if (mysqli_num_rows($result)>0){
			while($r=mysqli_fetch_assoc($result)){
				array_push($rows, $r);	
				echo '<pre> ids'; print_r($rows); echo '</pre>';
				echo '<pre> r'; print_r($r); echo '</pre>';
				$id = $r['max(id)'];
				//$id=0;
echo '<pre> r:'; print_r($id); echo '</pre>';
			}
			echo '<pre> r:'; print_r($id); echo '</pre>';
		}
		
		if(! $id){$id=0;}echo '<pre> id:'; print_r($id); echo '</pre>';
		return $id;	
	}
	
	if ($id) {
		echo "have id".Sid;
		
	}else{
		echo "no id";
		//$id = 1;
	}
	echo" row: ";
	echo $row;
	echo" id: ";
	echo $id;
	return $id;
	
}

function prepareSTMT($tabela){
	if ($tabela == "medicao_log") {
		return	
			"INSERT INTO medicao_log (
				idMedicao,
				DataHoraMedicao,
				ValorMedicao,
				FK_IDCultura,
				FK_IDVariavel,
				utilizador,
				data_operacao,
				operacao,
				id
			)VALUES (?,?,?,?,?,?,?,?,?)" ;
	}
	else if($tabela == "investigador_log") {
		return
			"INSERT INTO investigador_log (
				idInvestigador,
				Email,
				CategoriaProfissional,
				Nome,
				ativo,				
				utilizador,
				data_operacao,
				operacao,
				id
			)
				VALUES (?,?,?,?,?,?,?,?,?)" ;
	}
	else if($tabela == "medicaotemperatura_log") {
		return	
			"INSERT INTO MedicaoTemperatura_log (
				DataHoraMedicao,
				ValorMedicao,
				IDMedicao,
				utilizador,
				data_operacao,
				operacao,
				id				
			)
				VALUES (?,?,?,?,?,?,?)" ;
	}
	else if($tabela == "variavel_log") {
		return	
			"INSERT INTO Variavel_log (
				IDVariavel,
				NomeVariavel,
				utilizador,
				data_operacao,
				operacao,
				id
				
				)
				VALUES (?,?,?,?,?,?)" ;
	}
	else if($tabela == "MedicaoLuminosidade_log") {
		return
			"INSERT INTO MedicaoLuminosidade_log (
				DataHoraMedicao,
				ValorMedicao,
				IDMedicao,
				utilizador,
				data_operacao,
				operacao,
				id
			
				)
				VALUES (?,?,?,?,?,?,?)" ;
	}
	else if($tabela == "Cultura_log") {
		return
			"INSERT INTO Cultura_log (
				IDCultura,
				NomeCultura,
				DescricaoCultura,
				FK_IDInvestigador,
				utilizador,				
				operacao,
				data_operacao,
				id				
				)
				VALUES (?,?,?,?,?,?,?,?)" ;
	}
	else if($tabela == "Sistema_log") {
		return
			"INSERT INTO Sistema_log (
				LimiteInferiorLuz,
				LimiteInferiorTemperatura, 
				LimiteSuperiorTemperatura,
				LimiteSuperiorLuz,
				utilizador,
				data_operacao,
				operacao,
				id
				
				)
				VALUES (?,?,?,?,?,?,?,?)" ;
	}
}

function bindSTMT($tabela, $stmt,$row){
	  // Bind parameters. Types: s = string, i = integer, d = double,  b = blob
	  echo '<pre>'; print_r($row); echo '</pre>';
	 if ($tabela == "Medicao_log"){
		return
			$stmt->bind_param('isdiisssi',
				$row[idInvestigador],				
				$row[Email],
				$row[CategoriaProfissional],
				$row[Nome],
				$row[ativo],
				$row[utilizador],
				$row[data_operacao],
				$row[operacao],
				$row[id]
				
			);
			
	}
	else if($tabela == "investigador_log") {
		echo"bind as investigador";
		return	
			
			$stmt->bind_param('isssisssi',
				$row[idInvestigador],
				$row[Email],
				$row[CategoriaProfissional],
				$row[Nome],
				$row[ativo],
				$row[utilizador],
				$row[data_operacao],
				$row[operacao],
				$row[id]
				
			);
			
	}
	else if($tabela == "MedicaoTemperatura_log") {
		return
			$stmt->bind_param('sdisssi',
				$row[DataHoraMedicao],					
				$row[ValorMedicao],
				$row[IDMedicao],
				$row[utilizador],
				$row[data_operacao],
				$row[operacao],
				$row[id]
			
			);
	}
	
	else if($tabela == "Variavel_log") {
		return	
			$stmt->bind_param('issssi',
				$row[IDVariavel],
				$row[NomeVariavel],
				$row[utilizador],
				$row[data_operacao],
				$row[operacao],
				$row[id]
			);
			
	}
	else if($tabela == "MedicaoLuminosidade_log") {
		return
			$stmt->bind_param('sdisssi',
				$row[DataHoraMedicao],
				$row[ValorMedicao],
				$row[IDMedicao],
				$row[utilizador],
				$row[data_operacao],
				$row[operacao],
				$row[id]
			);
			
	}
	else if($tabela == "Cultura_log") {
		return
			$stmt->bind_param('ississsi',
				$row[IDCultura],
				$row[NomeCultura],
				$row[DescricaoCultura],
				$row[FK_IDInvestigador],
				$row[utilizador],
				$row[operacao],
				$row[data_operacao],
				$row[id]
			);
			
	}
	else if($tabela == "Sistema_log") {
		return
			$stmt->bind_param('ddddsssi',				
				$row[LimiteInferiorLuz],
				$row[LimiteInferiorTemperatura],
				$row[LimiteSuperiorTemperatura],
				$row[LimiteSuperiorLuz],
				$row[utilizador],
				$row[data_operacao],
				$row[operacao],
				$row[id]
			);
	}
	
}


function InsertintoLogs($connDestino, $data, $tabela){
$ready = '';
$fail = '';
 
$data = array_filter($data);
 

 
 // statement .tabela()
$sql=prepareSTMT($tabela);
echo"STM sql:"; echo $sql; 
$stmt = $connDestino->prepare($sql);
 
// Check if prepare() failed.
if ( false === $stmt ) {
    echo 'prepare() failed: ' . htmlspecialchars($stmt->error);
    trigger_error($connDestino->error, E_USER_ERROR);
}
 
$connDestino->query("START TRANSACTION");
 

 
 foreach ($data as $row) {
   $bind = bindSTMT($tabela,$stmt,$row);
	
	      
    // Check if bind_param() failed.
    if ( false === $bind ) {
        echo 'bind_param() failed: ' . htmlspecialchars($stmt->error);
    }
    $exec = $stmt->execute();
 
    // Check if execute() failed.
    if ( false === $exec ) {
        $fail .= sprintf("%s will not be inserted because execute() failed: %s<br />", $row[0], htmlspecialchars($stmt->error));
    } else {
        $ready .= sprintf("%s will be inserted in database.<br />", $row[0]);
    }
 
}
 
// Close the prepared statement
$stmt->close();
 
if ( ! empty( $ready ) )
    echo $ready;
if ( ! empty( $fail ) )
    echo $fail;
 
$commit = $connDestino->query("COMMIT");
 
if ( false === $commit ) {
    echo "Transaction commit failed<br />";
}
 
echo "<br />End of insert into $tabela .<br />";
 

}

?>