<?php
    namespace App\Model;

    use App\Lib\Database;
    use App\Lib\Response;

    class ProductoModel
    {
        private $db;
        private $table = 'api_pedido_productos';
        private $response;
        public $media_base_url = "http://192.168.10.116:81/www/coronaApi/uploads/img/vinos/";
        
        public function __CONSTRUCT()
        {
            $this->db = Database::StartUp();
            $this->response = new Response();
        }
        


        public function searchProducts($tag,$usuario_id)
        {
            try 
            {
                 $myTag = $tag;
                 $myUsuario = $usuario_id;
                 $dist = 5;

                 if($myTag==0){
                   $sql="SELECT DISTINCT productos.*, CONCAT('".$this->media_base_url."',productos.ruta_imagen, productos.imagen) as imagenProducto, CONCAT(SUBSTRING(productos.descripcion,1,52),'...') as descripcion
                          FROM productos
                          INNER JOIN producto_establecimiento ON producto_establecimiento.producto_id=productos.id
                          ORDER BY productos.nombre"; 
                 }else{
                        $sql="SELECT DISTINCT productos.*, CONCAT('".$this->media_base_url."',productos.ruta_imagen, productos.imagen) as imagenProducto, CONCAT(SUBSTRING(productos.descripcion,1,52),'...') as descripcion
                          FROM productos
                          INNER JOIN producto_establecimiento ON producto_establecimiento.producto_id=productos.id
                          INNER JOIN productos_tags ON productos_tags.producto_id = productos.id
                           INNER JOIN tags ON tags.id = productos_tags.tag_id
                           WHERE tags.nombre LIKE '%$myTag%' and tags.estatus = 1 and productos.estatus = 1 and productos_tags.estatus
                          ORDER BY productos.nombre"; 

                   /*$sql = "SELECT productos.id, productos.nombre, productos.descripcion FROM productos 
                           INNER JOIN productos_tags ON productos_tags.producto_id = productos.id
                           INNER JOIN tags ON tags.id = productos_tags.tag_id
                           WHERE tags.nombre LIKE '%$myTag%' and tags.estatus = 1 and productos.estatus = 1 and productos_tags.estatus = 1
                           ORDER BY productos.nombre;";*/
                 }

                $stmt = $this->db->prepare($sql);
                $result = $stmt->execute();
                $obj = (object) array('code' => 9997, 'message'=> '');
                
                if($result) {
                    $res_arr =  $stmt->fetchAll();
                    if($res_arr == false){
                        $obj->code = 9997;
                        $obj->message = 'No se encontraron productos';
                    }else{
                        $obj->code = 9998;
                        $obj->message = 'Success';
                        $obj->vinos = $res_arr;
                    }
                }else{
                    $obj->code = 9999;
                    $obj->message = 'Error al buscar producto';
                }
                $this->response->setResponse(true);
                $this->response = $obj;
                return $this->response;
            }catch (Exception $e) 
            {
                $this->response->setResponse(false, $e->getMessage());
            }
        }



                public function wineDetail($producto_id, $usuario_id)
        {

            //https://maps.googleapis.com/maps/api/place/nearbysearch/json?name=oxxo|7eleven|walmart&location=25.7638805,-100.2778148&radius=1000&key=AIzaSyDeJHtLTDUGPVz_gid0dJee8uxgeqC_OjY
/*
            $url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?name=oxxo|7eleven|walmart&location=25.7638805,-100.2778148&radius=1000&key=AIzaSyDeJHtLTDUGPVz_gid0dJee8uxgeqC_OjY";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
            // This is what solved the issue (Accepting gzip encoding)
            curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");     
            $response = curl_exec($ch);
            curl_close($ch);
            $googleResponse = json_decode($response); 

            print_r($googleResponse); die;*/
            try 
            {
                 $myProducto = $producto_id;
                 $myUsuario = $usuario_id;
                 $dist = 5;

                 $sql = "SELECT * FROM establecimientos  
                        LEFT JOIN producto_establecimiento ON producto_establecimiento.establecimiento_id=establecimientos.id
                        WHERE producto_establecimiento.producto_id = $myProducto";

                 $sql2 = "SELECT * FROM usuario_direcciones WHERE usuario_id = $myUsuario AND estatus = 1";

                 $sql3 = "Select * FROM productos WHERE id = $myProducto";

                 $sql4 = "SELECT CONCAT(ruta_imagen, imagen) AS imagen FROM `producto_imagenes` WHERE producto_id = $myProducto";





                $stmt = $this->db->prepare($sql);
                $stmt2 = $this->db->prepare($sql2);
                $stmt3 = $this->db->prepare($sql3);
                $stmt4 = $this->db->prepare($sql4);

                $result = $stmt->execute();
                $result2 = $stmt2->execute();
                $result3 = $stmt3->execute();
                $result4 = $stmt4->execute();

                $res_arr =  $stmt->fetchAll();
                $obj = (object) array('code' => 9997, 'message'=> '');
                if($res_arr == false){
                    $obj->code = 9997;
                    $obj->message = 'No se encontraron productos';
                }else{
                    $obj->code = 9998;
                    $obj->message = 'Success';
                    $res_arr3 = $stmt3->fetch();
                    $obj->nombre = $res_arr3->nombre;
                    $obj->descripcion = $res_arr3->descripcion;
                    //$obj->ubicaciones = $res_arr;
                    $establecimientos = "";
                    foreach ($res_arr as $key => $value) {
                        $establecimientos.=str_replace(' ', '%20', $value->nombre)."|";
                        
                    }

                    $res_arr2 =  $stmt2->fetch();
                    $miUbicacion=  $res_arr2->latitud.",".$res_arr2->longitud; 
                    //echo $miUbicacion; die;
                    $url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?name=".$establecimientos."&location=".$miUbicacion."&radius=1000&key=AIzaSyDeJHtLTDUGPVz_gid0dJee8uxgeqC_OjY";

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
                    // This is what solved the issue (Accepting gzip encoding)
                    curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");     
                    $response = curl_exec($ch);
                    curl_close($ch);
                    $googleResponse = json_decode($response); 

                   // print_r($googleResponse->results); die;



                    $misDestinos = array();


                    foreach ($googleResponse->results as $key => $value) {
                        
                        $newDestino = array(
                            "nombre" => $value->name,
                            "longitud" => $value->geometry->location->lng,
                            "latitud" => $value->geometry->location->lat,
                            "direccion" => $value->vicinity
                        );

                        array_push($misDestinos, $newDestino);
                    }
                    $obj->ubicaciones = $misDestinos;
                    //print_r($misDestinos); die;

                    $res_arr4 =  $stmt4->fetchAll();
                    if(!empty($res_arr4)){
                        
                        $obj->imagenes = $res_arr4;
                    }else{
                        $miImagen = array();
                        $newImagen = array(
                            "imagen" => $this->media_base_url."otros/notFoto.png"
                        );

                        array_push($miImagen, $newImagen);
                        $obj->imagenes = $miImagen;
                    }
                }


                /*
                 $sql = "SELECT DISTINCT establecimiento_direcciones.*, SUBSTRING(3956 * 2 * ASIN(SQRT( POWER(SIN((usuario_direcciones.latitud -establecimiento_direcciones.latitud) * pi()/180 / 2), 2) +COS(usuario_direcciones.latitud * pi()/180) * COS(establecimiento_direcciones.latitud * pi()/180) *POWER(SIN((usuario_direcciones.longitud -establecimiento_direcciones.longitud) * pi()/180 / 2), 2) )), 1, 5) as distance 
                    FROM usuario_direcciones, establecimiento_direcciones
                    LEFT JOIN producto_establecimiento ON producto_establecimiento.establecimiento_direccion_id=establecimiento_direcciones.id
                    INNER JOIN productos ON productos.id = producto_establecimiento.producto_id
                    INNER JOIN productos_tags ON productos_tags.producto_id = productos.id
                    WHERE productos.id = $myProducto and usuario_direcciones.usuario_id = $myUsuario and usuario_direcciones.estatus = 1
                    and establecimiento_direcciones.longitud between (usuario_direcciones.longitud-".$dist."/abs(cos(radians(usuario_direcciones.latitud))*69)) and (usuario_direcciones.longitud+".$dist."/abs(cos(radians(usuario_direcciones.latitud))*69)) 
                    and establecimiento_direcciones.latitud between (usuario_direcciones.latitud-(".$dist."/69)) and (usuario_direcciones.latitud+(".$dist."/69))
                    ORDER BY Distance limit 10;"; 

                 $sql2 = "Select * FROM productos WHERE id = $myProducto";

                 $sql3 = "SELECT CONCAT(ruta_imagen, imagen) AS imagen FROM `producto_imagenes` WHERE producto_id = $myProducto";



                $stmt = $this->db->prepare($sql);
                $stmt2 = $this->db->prepare($sql2);
                $stmt3 = $this->db->prepare($sql3);

                $result = $stmt->execute();
                $result2 = $stmt2->execute();
                $result3 = $stmt3->execute();

                $obj = (object) array('code' => 9997, 'message'=> '');
                if($result2){
                    $res_arr2 = $stmt2->fetch();
                    $obj->nombre = $res_arr2->nombre;
                    $obj->descripcion = $res_arr2->descripcion;

                    if($result) {
                        $res_arr =  $stmt->fetchAll();
                        if($res_arr == false){
                            $obj->code = 9997;
                            $obj->message = 'No se encontraron productos';
                        }else{
                            $obj->code = 9998;
                            $obj->message = 'Success';
                            $obj->ubicaciones = $res_arr;

                            if($result3){
                                $res_arr3 =  $stmt3->fetchAll();
                                $obj->imagenes = $res_arr3;
                            }
                        }
                    }else{
                        $obj->code = 9999;
                        $obj->message = 'Error al buscar producto';
                    }
                }*/
                $this->response->setResponse(true);
                $this->response = $obj;
                return $this->response;
            }catch (Exception $e) 
            {
                $this->response->setResponse(false, $e->getMessage());
            }
        }

/*
        public function wineDetail($producto_id, $usuario_id)
        {
            try 
            {
                 $myProducto = $producto_id;
                 $myUsuario = $usuario_id;
                 $dist = 5;

                 $sql = "SELECT DISTINCT establecimiento_direcciones.*, SUBSTRING(3956 * 2 * ASIN(SQRT( POWER(SIN((usuario_direcciones.latitud -establecimiento_direcciones.latitud) * pi()/180 / 2), 2) +COS(usuario_direcciones.latitud * pi()/180) * COS(establecimiento_direcciones.latitud * pi()/180) *POWER(SIN((usuario_direcciones.longitud -establecimiento_direcciones.longitud) * pi()/180 / 2), 2) )), 1, 5) as distance 
                    FROM usuario_direcciones, establecimiento_direcciones
                    LEFT JOIN producto_establecimiento ON producto_establecimiento.establecimiento_direccion_id=establecimiento_direcciones.id
                    INNER JOIN productos ON productos.id = producto_establecimiento.producto_id
                    INNER JOIN productos_tags ON productos_tags.producto_id = productos.id
                    WHERE productos.id = $myProducto and usuario_direcciones.usuario_id = $myUsuario and usuario_direcciones.estatus = 1
                    and establecimiento_direcciones.longitud between (usuario_direcciones.longitud-".$dist."/abs(cos(radians(usuario_direcciones.latitud))*69)) and (usuario_direcciones.longitud+".$dist."/abs(cos(radians(usuario_direcciones.latitud))*69)) 
                    and establecimiento_direcciones.latitud between (usuario_direcciones.latitud-(".$dist."/69)) and (usuario_direcciones.latitud+(".$dist."/69))
                    ORDER BY Distance limit 10;"; 

                 $sql2 = "Select * FROM productos WHERE id = $myProducto";

                 $sql3 = "SELECT CONCAT(ruta_imagen, imagen) AS imagen FROM `producto_imagenes` WHERE producto_id = $myProducto";



                $stmt = $this->db->prepare($sql);
                $stmt2 = $this->db->prepare($sql2);
                $stmt3 = $this->db->prepare($sql3);

                $result = $stmt->execute();
                $result2 = $stmt2->execute();
                $result3 = $stmt3->execute();

                $obj = (object) array('code' => 9997, 'message'=> '');
                if($result2){
                    $res_arr2 = $stmt2->fetch();
                    $obj->nombre = $res_arr2->nombre;
                    $obj->descripcion = $res_arr2->descripcion;

                    if($result) {
                        $res_arr =  $stmt->fetchAll();
                        if($res_arr == false){
                            $obj->code = 9997;
                            $obj->message = 'No se encontraron productos';
                        }else{
                            $obj->code = 9998;
                            $obj->message = 'Success';
                            $obj->ubicaciones = $res_arr;

                            if($result3){
                                $res_arr3 =  $stmt3->fetchAll();
                                $obj->imagenes = $res_arr3;
                            }
                        }
                    }else{
                        $obj->code = 9999;
                        $obj->message = 'Error al buscar producto';
                    }
                }
                $this->response->setResponse(true);
                $this->response = $obj;
                return $this->response;
            }catch (Exception $e) 
            {
                $this->response->setResponse(false, $e->getMessage());
            }
        }
*/
        
    }