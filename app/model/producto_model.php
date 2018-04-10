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