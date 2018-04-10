<?php
    namespace App\Model;

    use App\Lib\Database;
    use App\Lib\Response;

    class UsuarioModel
    {
        private $db;
        private $table = 'usuarios';
        private $response;
        
        public function __CONSTRUCT()
        {
            $this->db = Database::StartUp();
            $this->response = new Response();
        }
        


        public function login($data)
        {
            try 
            {
                $myusername = $data['Email'];
                $mypassword = $data['Password'];

                $sql = "SELECT id FROM usuarios WHERE correo = '$myusername' and password = '$mypassword'";

                $stmt = $this->db->prepare($sql);
                $result = $stmt->execute();
                $obj = (object) array('code' => 9997, 'message'=> '');
                
                if($result) {
                    $res_arr =  $stmt->fetchAll();
                    if($res_arr == false){
                        $obj->code = 9997;
                        $obj->message = 'usuario y/o contraseña incorrectos';
                    }else{
                        $obj->code = 9998;
                        $obj->message = 'Success';
                        $obj->id = $res_arr[0]->id;
                        $obj->access_token = "22dd3f33gg";
                    }
                }else{
                    $obj->code = 9999;
                    $obj->message = 'Error al iniciar sesión';
                }
                $this->response->setResponse(true);
                $this->response = $obj;
                return $this->response;
            }catch (Exception $e) 
            {
                $this->response->setResponse(false, $e->getMessage());
            }
        }


        public function getAdresses($usuario_id)
        {
            try 
            {
                $usuario = $usuario_id;

                $sql = "SELECT CASE WHEN LENGTH(nombre) > 36 
                       THEN CONCAT(SUBSTRING(nombre, 1, 36), '...')
                       ELSE  nombre END AS nombre , id, longitud, latitud FROM usuario_direcciones 
                       WHERE usuario_id='$usuario' 
                       ORDER BY id DESC 
                       limit 3;";

                $stmt = $this->db->prepare($sql);
                $result = $stmt->execute();
                $obj = (object) array('code' => 9997, 'message'=> '');
                
                if($result) {
                    $res_arr =  $stmt->fetchAll();
                    if($res_arr == false){
                        $obj->code = 9997;
                        $obj->message = 'No se encontraron direcciones';
                    }else{
                        $obj->code = 9998;
                        $obj->message = 'Success';
                        $obj->direcciones = $res_arr;
                    }
                }else{
                    $obj->code = 9999;
                    $obj->message = 'Error al iniciar sesión';
                }
                $this->response->setResponse(true);
                $this->response = $obj;
                return $this->response;
            }catch (Exception $e) 
            {
                $this->response->setResponse(false, $e->getMessage());
            }
        }

        public function getMainAdress($usuario_id)
        {
            try 
            {
                $usuario = $usuario_id;

                $sql = "SELECT CASE WHEN LENGTH(nombre) > 36 
                       THEN CONCAT(SUBSTRING(nombre, 1, 36), '...')
                       ELSE  nombre END AS nombre , id, longitud, latitud FROM usuario_direcciones 
                       WHERE usuario_id='$usuario' and estatus = 1
                       ORDER BY id DESC 
                       limit 1;";

                $stmt = $this->db->prepare($sql);
                $result = $stmt->execute();
                $obj = (object) array('code' => 9997, 'message'=> '');
                
                if($result) {
                    $res_arr =  $stmt->fetch();
                    if($res_arr == false){
                        $obj->code = 9997;
                        $obj->message = 'No se encontraron direcciones';
                    }else{
                        $obj = $res_arr;
                        $obj->code = 9998;
                        $obj->message = 'Success';
                        
                    }
                }else{
                    $obj->code = 9999;
                    $obj->message = 'Error al iniciar sesión';
                }
                $this->response->setResponse(true);
                $this->response = $obj;
                return $this->response;
            }catch (Exception $e) 
            {
                $this->response->setResponse(false, $e->getMessage());
            }
        }


        public function saveUserAddress($data)
        {
            try 
            {


                    $obj = (object) array('code' => 9997, 'message'=> '');
                    if(isset($data["Longitud"])&&isset($data["Latitud"])){

                    $sql2 = "UPDATE usuario_direcciones set estatus = ? WHERE usuario_id = ?; ";
                    $this->db->prepare($sql2)
                        ->execute(
                            array(
                                0, 
                                $data['Usuario_Id']
                            )
                        );

                        $sql = "INSERT INTO usuario_direcciones (calle, numero, colonia, municipio, estado, pais, codigo_postal, longitud, latitud, estatus, usuario_id, nombre) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,?, ?, ?)";

                         $longitud = $data['Longitud'];
                         $latitud = $data['Latitud']; 

                         $calle = (isset($data['Calle'])) ? $data['Calle'] : null; 
                         $numero = (isset($data['Numero'])) ? $data['Numero'] : null; 
                         $colonia = (isset($data['Colonia'])) ? $data['Colonia'] : null; 
                         $municipio = (isset($data['Municipio'])) ? $data['Municipio'] : null; 
                         $estado = (isset($data['Estado'])) ? $data['Estado'] : null; 
                         $pais = (isset($data['Pais'])) ? $data['Pais'] : null; 
                         $codigo_postal = (isset($data['Codigo_Postal'])) ? $data['Codigo_Postal'] : null; 
                         $usuario_id = (isset($data['Usuario_Id'])) ? $data['Usuario_Id'] : null; 
                         $nombre = (isset($data['Nombre'])) ? $data['Nombre'] : null; 
                        
                        $this->db->prepare($sql)
                            ->execute(
                                array(
                                    $calle, 
                                    $numero,
                                    $colonia,
                                    $municipio,
                                    $estado,
                                    $pais,
                                    $codigo_postal,
                                    $longitud,
                                    $latitud,
                                    1,
                                    $usuario_id,
                                    $nombre
                                )
                            ); 
                    
                    $obj->code = 1;
                    $obj->message = 'Success';
                    $this->response = $obj;
                    return $this->response;
                }else{
                    $obj->code = 0;
                    $obj->message = 'Failed';
                    $this->response = $obj;
                    return $this->response; 
                }
            }catch (Exception $e) 
            {
                $this->response->setResponse(false, $e->getMessage());
                $obj->code = -1;
                $obj->message = 'Error';
                $this->response = $obj;
            }
        }


        public function setAddress($usuario_id, $direccion_id)
        {
            try 
            {
                    $obj = (object) array('code' => 9997, 'message'=> '');

                    $sql1 = "UPDATE usuario_direcciones set estatus = ? WHERE usuario_id = ?; ";
                    $sql2 = "UPDATE usuario_direcciones set estatus = ? WHERE id = ?; ";
                    
                    $this->db->prepare($sql1)
                        ->execute(
                            array(
                                0, 
                                $usuario_id
                            )
                        );

                        $this->db->prepare($sql2)
                        ->execute(
                            array(
                                1,
                                $direccion_id
                            )
                        );
                
                
                $this->response->setResponse(true);
                $obj->code = 1;
                $obj->message = 'Success';
                $this->response = $obj;
                return $this->response;
            }catch (Exception $e) 
            {
                $this->response->setResponse(false, $e->getMessage());
            }
        }


        public function InsertOrUpdate($data)
        {
            try 
            {
                if(isset($data['id']))
                {
                    $sql = "UPDATE $this->table SET 
                                correo          = ?, 
                                password        = ?,
                                estatus          = ?
                            WHERE id = ?";
                    
                    $this->db->prepare($sql)
                        ->execute(
                            array(
                                $data['Email'], 
                                $data['Password'],
                                $data['Estatus'],
                                $data['Id']
                            )
                        );
                }
                else
                {
                    $sql = "INSERT INTO $this->table
                                (correo, password, estatus)
                                VALUES (?,?,?)";
                    
                    $this->db->prepare($sql)
                        ->execute(
                            array(
                                $data['Email'], 
                                $data['Password'],
                                1
                            )
                        ); 
                }
                
                $this->response->setResponse(true);
                return $this->response;
            }catch (Exception $e) 
            {
                $this->response->setResponse(false, $e->getMessage());
            }
        }

        
    }