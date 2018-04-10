<?php
    namespace App\Model;

    use App\Lib\Database;
    use App\Lib\Response;

    class TagModel
    {
        private $db;
        private $table = 'tags';
        private $response;
        
        public function __CONSTRUCT()
        {
            $this->db = Database::StartUp();
            $this->response = new Response();
        }
        


        public function searchTags($tag)
        {
            try 
            {
                $sql = "SELECT tags.nombre, tags.id FROM tags 
                       WHERE tags.nombre LIKE '%$tag%' and tags.estatus = 1 
                       ORDER BY tags.nombre;";

                $stmt = $this->db->prepare($sql);
                $result = $stmt->execute();
                $obj = (object) array('code' => 9997, 'message'=> '');
                
                if($result) {
                    $res_arr =  $stmt->fetchAll();
                    if($res_arr == false){
                        $obj->code = 9997;
                        $obj->message = 'No se encontraron tags';
                    }else{
                        $obj->code = 9998;
                        $obj->message = 'Success';
                        $obj->tags = $res_arr;
                    }
                }else{
                    $obj->code = 9999;
                    $obj->message = 'Error al buscar tags';
                }
                $this->response->setResponse(true);
                $this->response = $obj;
                return $this->response;
            }catch (Exception $e) 
            {
                $this->response->setResponse(false, $e->getMessage());
            }
        }

        
    }