<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bd_ekousuario";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo "conexion fail";
    die("Connection failed: " . $conn->connect_error);
}

$mensaje = "";
$errors = [];

$nombre = $apellidos = $correo = $cp = $estado = $alcaldia = $colonia = $contra = $confirmarcontra = $telefono = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $nombre = strtoupper($_POST['nombre']);
    $apellidos = strtoupper($_POST['apellido']);
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $contra = $_POST['contrasena']; 
    $confirmarcontra = $_POST['contrasena-confrm'];
    $cp = $_POST['cp'];
    $estado = $_POST['estado'];
    $alcaldia = $_POST['alcaldia'];
    $colonia = $_POST['colonia'];
     //$apellidos $correo  $cp $estado $alcaldia $colonia $contra  $confirmarcontra  $telefono;
     
     if (!preg_match("/^.{8,32}$/", $contra)) {
        $errors['contra'] = "Por favor, ingresa una contraseña válida.";
    }

    if ($contra != $confirmarcontra) {
        $errors['confirmarcontra'] = "Las contraseñas no coinciden";
    } 

    if (!preg_match("/^[A-ZÁ-ÿ\s]{1,40}$/", $nombre)) {
        $errors['nombre'] = "El nombre solo puede contener letras.";
    }

    if (!preg_match("/^\d{10}$/", $telefono)) {
        $errors['telefono'] = "El telefono debe de ser de 10 dígitos";
    }
    if (!preg_match("/^\d{5}$/", $cp)) {
        $errors['cp'] = "El Código Postal debe contener 5 dígitos";
    }

    if (!preg_match("/^[A-ZÁ-ÿ\s]{1,80}$/", $apellidos)) {
        $errors['apellidos'] = "El apellido solo puede contener letras, evite abreviaciones.";
    }

    if (!preg_match("/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9]+\.[a-zA-Z0-9-.]+$/", $correo)) {
        $errors['correo'] = "Formato de correo electrónico inválido";
    }   

    $sql_check_email = "SELECT * FROM tab_ekousuario WHERE correo_ekou = '$correo'";
    $result_check_email = $conn->query($sql_check_email);
    if ($result_check_email->num_rows > 0) {
        $errors['correo'] = "Este correo electrónico ya está registrado.";
    } 

    if (empty($errors)) {
        $contra = md5($contra); 
        $sql = "INSERT INTO tab_ekousuario (nombre_ekou, apellido_ekou, correo_ekou, telefono_ekou, cp_ekou, estado_ekou, alcaldia_municipio_ekou, colonia_ekou, contrasena_ekou, fec_reg_ekou, hr_reg_ekou)
        VALUES ('$nombre', '$apellidos', '$correo', '$telefono', '$cp', '$estado', '$alcaldia', '$colonia', '$contra', NOW(), CURTIME())";
       

        if ($conn->query($sql) === TRUE) {
            $mensaje = "Registro exitoso";
            $nombre = $apellidos = $correo = $telefono = $cp = $estado = $alcaldia = $colonia = $contra = $confirmarcontra = "";
    
        } else {
            $mensaje = "Error al registrar: " . $conn->error;
           
        }
    } else {

    }
}

$sql_colonias = "SELECT * FROM cat_colonia";
$result_colonias = $conn->query($sql_colonias);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/2f789264ca.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="syles3.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Ekolana</title>

    <style>  
    body {
        /*margin: 0;
        padding: 0;*/
        box-sizing: border-box;
    }
    h2, h3 {
        text-align: center;
        color: #218838;
    }

    h2 {
        font-size: 30px;
    }

    h3 {
        font-size: 25px;
    }

    hr {
        background: linear-gradient(90deg, rgba(33,136,56,1) 0%, rgba(255,255,255,1) 50%, rgba(33,136,56,1) 100%);
        height: 3px;
    }
    form {
        display: flex;
        max-width: 900px;
        margin: 0 auto;
        padding: 5px;
        background-color: #f5f5d0;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        align-items: center;
        flex-direction: column;
    }

    .campo {
        display: flex;
        width: 100%;
        /*background-color: #28a745*/
        padding-bottom: 0px;
        padding-left: 20px;
        flex-direction: column;
    }

    label {
        margin-bottom: 5px;
        padding: 0px;
        color: #555;
        font-size: x-large;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"],
    select {
        display: block;
        width: 80%;
        padding: 5px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: large;
    }

    .form_input:focus {
        border: 3px solid #0075FF;
        outline: none;
        box-shadow: 3px 0px 30px rgba(163,163,163, 0.4);
    }

    .formulario_input_error {
        font-size: 12px;
        margin-bottom: 0;
        text-align: justify;
        display: none;
    }

    .formulario_input_invalid_extension {
        font-size: 12px;
        margin-bottom: 0;
        text-align: justify;
        display: none;
    }

    .formulario_input_error_activo {
        display: block;
    }

    .btn_enviar {
        width: 30%;
        height: 45px;
        padding: 10px;
        background-color: #28a745;
        border: none;
        border-radius: 4px;
        color: #fff;
        font-size: 15px;
        cursor: pointer;
    }
    
    .btn_enviar:hover {
        background-color: #218838;
    }

    .formulario_mensaje {
        height: 45px;
        line-height: 45px;
        background: #F66060;
        padding: 0 15px;
        border-radius: 3px;
        display: none;
    }

    .formulario_mensaje_activo {
        height: 45px;
        font-size: 15px;
        line-height: 45px;
        padding: 0 15px;
        border-radius: 3px;
        text-align: center;
        color: #F66060;
        display: block;
    }

    .formulario_mensaje p {
        margin: 0;
    }

    /* ----- -----  Estilos para Validacion ----- ----- */
    /* ----- Inputs -----*/
    .campo_correcto .form_input {
        border: 3px solid #1ed12d;
    }

    .campo_incorrecto .etiqueta {
        color: #bb2929;
    }

    .campo_incorrecto .form_input  {
        border: 3px solid #bb2929;
    }

    /* ----- Select -----*/
    .campo_correcto .form_select {
        border: 3px solid #1ed12d;
    }

    .campo_incorrecto .form_select  {
        border: 3px solid #bb2929;
    }

    .formulario_mensaje_exito {
        font-size: 20px;
        color: #119200;
        display: none;
    }

    .formulario_mensaje_exito_activo {
        font-size: 20px;
        color: #119200;
        display: block;
        text-align: center;
    }

    @media (min-width: 720px) {
        form {
            padding: 10px;
        }
        hr {

            background: linear-gradient(90deg, rgba(127,247,154,1) 0%, rgba(33,136,56,1) 50%, rgba(127,247,154,1) 100%);
            height: 3px;
        }
        input[type="submit"] {
            font-size: 20px;
            width: 30%;
        }
        .campo {
            display: flex;
            width: 80%;
            padding: 5px;
            flex-direction: row;
            align-items: flex-start;
        }
        label {
            margin-bottom: 5px;
            padding-left: 50px;
            color: #555;
        }
        .campo .campo-texto{
            /*background-color: #555;*/
            width: 100%;
            align-self: right;
            padding-right: 50px;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            float:right;
            width: 80%;
        }

    }
    </style>
    </head>
    <body>
        <h3>Ekolana - Registro Ekousuario</h3>
        <hr>
        <!-- action="procesar_formulario.php" -->
        <?php if(isset($mensaje) && $mensaje !== ""): ?>
            <p class="formulario_mensaje_exito_activo" id="formulario_mensaje_exito_activo"><?php echo $mensaje; ?></p>
        <?php elseif(!empty($errors)): ?>
            <div class="formulario_mensaje_activo" id="formulario_mensaje_activo">
                    <p><b>Error:</b> Por favor llena el formulario correctamente.</p>
                    <p><?php echo $errors['correo']; ?></p>
            </div>
        <?php endif; ?>
        <form class="formulario" id="formulario"  method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <!-- Campo nombre -->
            <div class="campo" id="campo_nombre">
                <label for="nombre" class="etiqueta">Nombre:</label>
                <div class="campo-texto">
                    <input type="text" class="form_input" onkeyup="mayus(this);" id="nombre" name="nombre" required>
                </div>
                <p class="formulario_input_error">El nombre solo puede contener letras.</p>
            </div>
                    
            <!-- Campo apellido -->
            <div class="campo" id="campo_apellido">
                <label for="apellido" class="etiqueta">Apellido:</label>
                <div class="campo-texto">
                    <input type="text" class="form_input" onkeyup="mayus(this);" id="apellido" name="apellido" required>
                </div>
                <p class="formulario_input_error">El apellido solo puede contener letras, evite abreviaciones.</p>
            </div>
            
            <!-- Campo telefono -->
            <div class="campo" id="campo_telefono">
                <label for="telefono" class="etiqueta">Teléfono:</label>
                <div class="campo-texto">
                    <input type="text"class="form_input"  id="telefono" name="telefono" required>
                </div>
                <p class="formulario_input_error">El teléfono debe contener 10 dígitos.</p>
            </div>
                
            <!-- Campo cp -->
            <div class="campo" id="campo_cp">
                <label for="cp" class="etiqueta">Código Postal:</label>
                <div class="campo-texto">
                    <input type="text" class="form_input" id="cp" name="cp" required>
                </div>
                <p class="formulario_input_error">El Código Postal debe contener 5 dígitos</p>
                <p class="formulario_input_invalid_extension">Código Postal inválido. Capture nuevamente</p>
            </div>
                        
            <!-- Campo estado -->
            <div class="campo">
                <label for="estado">Estado:</label>
                <div class="campo-texto">
                    <input type="text" id="estado" name="estado" disabled>
                </div>
            </div>
            
            <!-- Campo alcaldia -->
            <div class="campo">
                <label for="alcaldia-municipio">Alcaldía/Municipio:</label>
                <div class="campo-texto">
                    <input type="text" id="alcaldia-municipio" name="alcaldia-municipio" disabled>
                </div>
            </div>
            
            <!-- Campo de selección colonia -->
            <div class="campo" id="campo_colonia">
                <label for="colonia" class="etiqueta">Colonia:</label>
                <div class="campo-texto">
                    <select class="form_select" id="colonia" name="colonia">
                        <option value="0">SELECCIONE UNA COLONIA</option>
                        <?php
                            if ($result_colonias->num_rows > 0) {
                                while($row = $result_colonias->fetch_assoc()) {
                                    $selected = ($row['colonia_cp'] == $colonia) ? "selected" : "";
                                    echo "<option value='".$row['colonia_cp']."' data-cp='".$row['cp_codigo_postal']."' data-estado='".$row['estado_cp']."' data-alcaldia='".$row['alcaldia_municipio_cp']."' $selected>".$row['colonia_cp']."</option>";
                                }
                            } else {
                                echo "<option value=''>No hay colonias disponibles</option>";
                            }
                        ?>
                    </select>
                        <?php if(isset($errors['colonia'])): ?>
                        <span class="error-msg"><?php echo $errors['colonia']; ?></span>
                        <?php endif; ?>
                </div>
                <p class="formulario_input_error">Debe seleccionar una de las opciones</p>
            </div>
                        
            <!-- Campo correo electronico -->
            <div class="campo" id="campo_correo">
                <label for="correo" class="etiqueta">Correo electrónico:</label>
                <div class="campo-texto">
                    <input type="email" class="form_input" id="correo" name="correo" placeholder="yo@ejemplo.com" required>
                </div>
                <p class="formulario_input_error">El correo solo puede contener letras, números, puntos, guiones y guión bajo.</p>
                <p class="formulario_input_invalid_extension">Extensión de correo inválida. Capture nuevamente.</p>
            </div>
                    
            <!-- Campo contraseña -->
            <div class="campo" id="campo_contrasena">
                <label class="etiqueta">Contraseña: <br> (8 - 32 caracteres)</label>
                <div class="campo-texto">
                    <input type="password" class="form_input" id="contrasena" name="contrasena" required>
                </div>
            </div>
            
            <!-- Campo confirmar contraseña -->
            <div class="campo" id="campo_contrasena_confrm">
                <label class="etiqueta">Confirmar contraseña:</label>
                <div class="campo-texto">
                    <input type="password" class="form_input" id="contrasena-confrm" name="contrasena-confrm" required>
                </div>
                <p class="formulario_input_error">La contraseña no coincide</p>
            </div>
                    
            <!-- Boton registrar -->
            <input type="submit" class="btn_enviar" value= "Registrar">
        </form>
    <script>
        const formulario = document.getElementById('formulario');
        const inputs = document.querySelectorAll('#formulario input');

        const validaciones = {
            nombre: /^[A-ZÁ-ÿ\s]{1,40}$/,
            apellido: /^[A-ZÁ-ÿ\s]{1,80}$/,
            correo: /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9]+\.[a-zA-Z0-9-.]+$/,
            telefono: /^\d{10}$/,
            cp: /^\d{5}$/,
            contrasena: /^.{8,32}$/,
        }

        function mayus(e) {
            e.value = e.value.toUpperCase();
        }

        const campos = {
            nombre: false,
            apellido: false,
            correo: false, 
            telefono: false,
            cp: false,
            colonia: false,
            contrasena: false,
        }

        const validarFormulario = (e) => {
            switch (e.target.name) {
                case "nombre":
                    validarCampo(validaciones.nombre, e.target, 'nombre');
                break;
                case "apellido":
                    validarCampo(validaciones.apellido, e.target, 'apellido');
                break;
                case "telefono":
                    validarCampo(validaciones.telefono, e.target, 'telefono');
                break;
                case "cp":
                    validarCampo(validaciones.cp, e.target, 'cp');
                break;
                case "correo":
                    validarCampo(validaciones.correo, e.target, 'correo');
                break;
                case "colonia":
                    validarSelect();
                break;
                case "contrasena":
                    validarCampo(validaciones.contrasena, e.target, 'contrasena');
                    validarContrasenaConfrm();
                break;
                case "contrasena-confrm":
                    validarContrasenaConfrm();
                break;
            }
        }

        const validarCampo = (expresion, input, campo) => {
            if (expresion.test(input.value)) {
                document.getElementById(`campo_${campo}`).classList.remove('campo_incorrecto');
                document.getElementById(`campo_${campo}`).classList.add('campo_correcto');
                if (campo != 'contrasena') {
                    document.querySelector(`#campo_${campo} .formulario_input_error`).classList.remove('formulario_input_error_activo');
                }
                campos[campo] = true;
                console.log(campos[colonia]);
            } else {
                document.getElementById(`campo_${campo}`).classList.remove('campo_correcto');
                document.getElementById(`campo_${campo}`).classList.add('campo_incorrecto');
                if (campo != 'contrasena') {
                    document.querySelector(`#campo_${campo} .formulario_input_error`).classList.add('formulario_input_error_activo');
                }
                campos[campo] = false;
                console.log(campos[colonia]);
            }
        }

        const validarContrasenaConfrm = () => {
            const inputContr1 = document.getElementById('contrasena');
            const inputContr2 = document.getElementById('contrasena-confrm');

            if (inputContr1.value !== inputContr2.value) {
                document.getElementById(`campo_contrasena_confrm`).classList.remove('campo_correcto');
                document.getElementById(`campo_contrasena_confrm`).classList.add('campo_incorrecto');
                document.querySelector(`#campo_contrasena_confrm .formulario_input_error`).classList.add('formulario_input_error_activo');
                campos[contrasena] = false;
                console.log(campos[colonia]);
            } else {
                document.getElementById(`campo_contrasena_confrm`).classList.add('campo_correcto');
                document.getElementById(`campo_contrasena_confrm`).classList.remove('campo_incorrecto');
                document.querySelector(`#campo_contrasena_confrm .formulario_input_error`).classList.remove('formulario_input_error_activo');
                campos[contrasena] = true;
                console.log(campos[colonia]);
            }
        }

        const validarSelect = () => {

            if (document.getElementById('colonia').selectedIndex === 0) {
                document.getElementById(`campo_colonia`).classList.remove('campo_correcto');
                document.getElementById(`campo_colonia`).classList.add('campo_incorrecto');
                document.querySelector(`#campo_colonia .formulario_input_error`).classList.add('formulario_input_error_activo');
                campos[colonia] = false;
                //console.log(campos[colonia]);
            } else{
                document.getElementById(`campo_colonia`).classList.add('campo_correcto');
                document.getElementById(`campo_colonia`).classList.remove('campo_incorrecto');
                document.querySelector(`#campo_colonia .formulario_input_error`).classList.remove('formulario_input_error_activo');
                campos[colonia] = true;
                //console.log(campos[colonia]);
            }
        }

        inputs.forEach((input) => {
            input.addEventListener('keyup', validarFormulario);
            input.addEventListener('blur', validarFormulario);
        });

        // event listener para select
        document.querySelector('#colonia').addEventListener('blur', validarFormulario);
        document.querySelector('#colonia').addEventListener('change', validarFormulario);

        formulario.addEventListener('submit', (e) => {
            // e.preventDefault(); //previene de que se ejecute el action
            
            console.log('campos: ');
            console.log(campos.nombre);
            console.log(campos.apellido);
            console.log(campos.telefono);
            console.log(campos.cp);
            console.log(campos.correo);
            console.log(campos.contrasena);
            if (campos.nombre && campos.apellido && campos.telefono && campos.cp && campos.correo && campos.contrasena) {
                //formulario.reset();
                formulario.unbind('submit').submit()
                document.getElementById('formulario_mensaje_exito').classList.add('formulario_mensaje_exito_activo');
                setTimeout(() => {
                    document.getElementById('formulario_mensaje_exito').classList.remove('formulario_mensaje_exito_activo')
                }, 5000);

                document.querySelectorAll('.campo_correcto').forEach((f_input) => {
                    f_input.classList.remove('campo_correcto');
                });
            } else {
                document.getElementById('formulario_mensaje').classList.add('formulario_mensaje_activo');
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
        const coloniaSelect = document.getElementById('colonia');
        const cpInput = document.getElementById('cp');
        const estadoInput = document.getElementById('estado');
        const alcaldiaInput = document.getElementById('alcaldia-municipio');
        coloniaSelect.addEventListener('change', function () {
            const selectedOption = coloniaSelect.options[coloniaSelect.selectedIndex];
            cpInput.value = selectedOption.getAttribute('data-cp');
            estadoInput.value = selectedOption.getAttribute('data-estado');
            alcaldiaInput.value = selectedOption.getAttribute('data-alcaldia');
        });
    });
     </script>
        </body>

</html>
<?php
    $conn->close();
?>