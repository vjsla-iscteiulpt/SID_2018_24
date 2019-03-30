<?php
function get_data($whereclause){
	$urlOrigem="127.0.0.1";
	$databaseOrigem="hotel";
	$usernameOrigem="root";
	$passwordOrigem="";

	$urlDestino="127.0.0.1";
	$databaseDestino="hotel";
	$usernameDestino="root";
	$passwordDestino="";
	
	$connOrigem = mysqli_connect($urlOrigem, $usernameOrigem, $passwordOrigem, $databaseOrigem);

	if (!$connOrigem){
		die ("Connection Failled: ".$connOrigem->connect_error);
	}
	
	$connDestino=mysqli_connect($urlDestino, $usernameDestino, $passwordDestino, $databaseDestino);
	
	if (!$connDestino){
		die ("Connection Failled: ".$connDestino->connect_error);
	}
	
	
	migrarTabela("Medicao_log",$connOrigem,$connDestino);
	
	migrarTabela("Investigador_log",$connOrigem,$connDestino);
	
	migrarTabela("MedicaoTemperatura_log",$connOrigem,$connDestino);
	
	migrarTabela("Variavel_log",$connOrigem,$connDestino);
	
	migrarTabela("MedicaoLuminosidade_log",$connOrigem,$connDestino);
	
	migrarTabela("Cultura_log",$connOrigem,$connDestino);
	
	migrarTabela("Sistema_log",$connOrigem,$connDestino);
	
	mysqli_close ($connOrigem);
	mysqli_close ($connDestino);
	
	//return json_encode($rows);
}
/*function InsertintoLogs($connDestino,$rows,$tabela){
		
	$query = "INSERT INTO table "$tabela" VALUES "(?);
	$stmt = $mysqli->prepare($query);
	$stmt ->bind_param("s", $one);

	$mysqli->query("START TRANSACTION");
	foreach ($array as $one) {
		$stmt->execute();
	}
	$stmt->close();
	$mysqli->query("COMMIT");
	//	mysqli_query($connDestino,$query)
	
	
}*/

function migrarTabela($tabela,$connOrigem,$connDestino){
	$id=getLastId($tabela,$connDestino);
	$rows = getLog($connOrigem, $id, $tabela);
	InsertintoLogs($connDestino,$rows,$tabela);
}

function getLog($connOrigem, $whereclause, $tabela){
	
	
	//$sql = "call migrate_".$tabela"(".$id")";
	 
	 $sql = "Select * from ".$tabela" where id >".$whereclause;
	
	$result = mysqli_query($connOrigem, $sql);
	$rows = array();
	if ($result) {
		if (mysqli_num_rows($result)>0){
			while($r=mysqli_fetch_assoc($result)){
				array_push($rows, $r);
			}
		}
		

		return $rows;	
	}
}

function getLastId($tabela, $connDestino){
	$sql= "Select max(id) from ".$tabela;
	$id = mysqli_query($connDestino, $sql);
	return $id;
	
}

function prepareSTMT($tabela){
	if (($tabela = "Medicao_log") {
		return	
			"INSERT INTO Medicao_log (
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
	else if($tabela = "Investigador_log") {
		return
			"INSERT INTO Investigador_log (
				idInvestigador,
				Email,
				CategoriaProfissionaln
				Nome,
				ativo,				
				utilizador,
				data_operacao,
				operacao,
				id
			)
				VALUES (?,?,?,?,?,?,?,?,?)" ;
	}
	else if($tabela = "MedicaoTemperatura_log") {
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
	else if($tabela = "Variavel_log") {
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
	else if($tabela = "MedicaoLuminosidade_log") {
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
	else if($tabela = "Cultura_log") {
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
	else if($tabela = "Sistema_log") {
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
	 $if ($tabela = "Medicao_log"){
		return
			$stmt->bind_param('isdiisssi',
				$row[0],
				$row[1],
				$row[2],
				$row[3],
				$row[4],
				$row[5],
				$row[6],
				$row[7],
				$row[8],
				$row[9]
			);
			
	}
	else if($tabela = "Investigador_log") {
		return	
			
			$stmt->bind_param('isssisssi',
				$row[0],
				$row[1],
				$row[2],
				$row[3],
				$row[4],
				$row[5],
				$row[6],
				$row[7],
				$row[8],
				$row[9]
			);
			
	}
	else if($tabela = "MedicaoTemperatura_log") {
		return
			$stmt->bind_param('sdisssi',
				$row[0],
				$row[1],
				$row[2],
				$row[3],
				$row[4],
				$row[5],
				$row[6],
				$row[7]
			
			);
	}
	
	else if($tabela = "Variavel_log") {
		return	
			$stmt->bind_param('issssi',
				$row[0],
				$row[1],
				$row[2],
				$row[3],
				$row[4],
				$row[5],
				$row[6]
			);
			
	}
	else if($tabela = "MedicaoLuminosidade_log") {
		return
			$stmt->bind_param('sdisssi',
				$row[0],
				$row[1],
				$row[2],
				$row[3],
				$row[4],
				$row[5],
				$row[6],
				$row[7]
			
			);
			
	}
	else if($tabela = "Cultura_log") {
		return
			$stmt->bind_param('ississsi',
				$row[0],
				$row[1],
				$row[2],
				$row[3],
				$row[4],
				$row[5],
				$row[6],
				$row[7],
				$row[8]
			);
			
	}
	else if($tabela = "Sistema_log") {
		return
			$stmt->bind_param('ddddsssi',
				$row[0],
				$row[1],
				$row[2],
				$row[3],
				$row[4],
				$row[5],
				$row[6],
				$row[7],
				$row[8]
			);
	}
	
}


function InsertintoLogs($connDestino, $data, $tabela){
$ready = '';
$fail = '';
 
$data = array_filter($data);
 /* Make database connection
 
//$connDestino = new mysqli("HOST","USERNAME","PASSWORD","DATABASE_NAME");
 
// Check connection
if ($connDestino->connect_errno) {
    echo 'Connect failed: ' . $connDestino->connect_error;
    exit();
}*/

 
 // statement .tabela()
$sql=prepareSTMT($tabela);
$stmt = $connDestino->prepare(.$sql);
 
// Check if prepare() failed.
if ( false === $stmt ) {
    echo 'prepare() failed: ' . htmlspecialchars($stmt->error);
    trigger_error($connDestino->error, E_USER_ERROR);
}
 
$connDestino->query("START TRANSACTION");
 
$bind = bindSTMT($tabela,$stmt,$row);
	
	      
    // Check if bind_param() failed.
    if ( false === $bind ) {
        echo 'bind_param() failed: ' . htmlspecialchars($stmt->error);
    }
 
 foreach ($data as $row) {
   
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
 
echo "<br />End of insert into ".$tabela".<br />";
 
/* Close the database connection
$connDestino->close();*/
}
?>

