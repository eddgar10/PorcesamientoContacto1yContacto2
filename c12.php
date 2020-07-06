 <?php
/*MODULO QUE EXTRAE NUMEROS TELEFONICOS DEL CAMPO REFERENCIAS CONFORMADO POR #,CADENA 

//DESCOMPONER CADENA:
//https://www.anerbarrena.com/php-explode-4656/*/

include __DIR__ . '/db_connect.php';

$nombretabla = "cuentas1y2".date('d_m_Y_g_i');

if(isset($_POST['import_data']))
    {
    
    // VALIDACION DE QUE EL ARCHIVO CARGADO ES UN FORMATO CSV O SIMILAR
    $file_mimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain', 'application/vnd.ms-excel');
    if(!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'],$file_mimes))
            {

            if(is_uploaded_file($_FILES['file']['tmp_name'])){
                $csv_file = fopen($_FILES['file']['tmp_name'], 'r');

                    $sql = "CREATE TABLE $nombretabla (
                        cuenta varchar(10),
                        c1 varchar(10),
                        c2 varchar(10))";
                if (mysqli_query($conn, $sql))
                    {
                      echo "Tabla $nombretabla creada";
                    } 
                else
                    {
                    echo "Error creating table: " . mysqli_error($conn);
                    }

                // MANEJO DATOS DE ARCHIVO CSV CARGADO PREVIAMENTE
                while(($emp_record = fgetcsv($csv_file)) !== FALSE)
                    {

//MANIPULACION DE CADENAS CAMPO CONTACTO 1 Y 2 PARA ASIGNAR COMO SOLO 1 NUMERO DE 10 DIGITOS
                     //SEGMENTA CADENA DE CAMPO 18 y 19 EN UN ARRAY DELIMITADO POR EL CARACTER ',' PASAR PARA ARRIBA Y HACER 1 SOLA CONSULTA SQL PARA LA INSERCION DE LOS DATOS YA PROCESADOS NUMERO DE CUENTA Y RELACIONADOS EN r1
                        $c1pre = $emp_record[18]; //el valor obtenido de csv pasa a un string
                        $c2pre = $emp_record[19]; //el valor obtenido de csv pasa a un string
                        
                        $arrc1pre = str_split($c1pre);
                        $arrc2pre = str_split($c2pre);
                        
                        $tamc1pre = count($arrc1pre);
                        $tamc2pre = count($arrc2pre);
                        
                        //echo "<br> $tamc1pre $tamc2pre";
                        echo "<br> contactos: ";
                        $limitediezc1 = 0;    //VARIABLE DELIMITANTE PARA GENERAR NUMERO A 10 DIGITOS PARA C1
                        $limitediezc2 = 0;    //VARIABLE DELIMITANTE PARA GENERAR NUMERO A 10 DIGITOS PARA C2
                        $c1="";             //VARIABLE CON NUMERO FINAL EXTRAIDO DEL STRING A 10 DIGITOS   
                        $c2="";             // LO QUE DICE EL DE ARRIBA
                        //CICLO CONTACTO1
                        for($i=0; $i<$tamc1pre ;$i++)
                        {
                            if(is_numeric($arrc1pre[$i]) && $limitediezc1 <10)
                                {
                                        $c1=$c1.$arrc1pre[$i];
                                        $limitediezc1 +=1;
                                }
                        }
                        //echo $c1;
                        //echo " ";
                    
                        //SI AL DE ARRIBA LE FUNCIONA A MI TAMBEN
                        for($i=0; $i<$tamc2pre ;$i++)
                        {
                            if(is_numeric($arrc2pre[$i]) && $limitediezc2 <10)
                                {

                                        $c2=$c2.$arrc2pre[$i];
                                        $limitediezc2 +=1;
                                }
                        }
                       // echo $c2;
                    }
        fclose($csv_file);
        $import_status = '?import_status=success';
            } 
        else
            {
                $import_status = '?import_status=error';
            }
    } 
    else 
        {
            $import_status = '?import_status=invalid_file';
        }
    }
header("Location: index.html".$import_status);

?>