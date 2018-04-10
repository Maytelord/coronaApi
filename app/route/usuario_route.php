<?php
use App\Model\UsuarioModel;

$app->group('/usuario/', function () {


    $this->post('addAccount', function ($req, $res) {
        $usMod = new UsuarioModel();
		
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $usMod->InsertOrUpdate(
                    $req->getParsedBody()
                )
            )
        );
    });

    $this->post('login', function ($req, $res) {
        $usMod = new UsuarioModel();
    
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $usMod->login(
                    $req->getParsedBody()
                )
            )
        );
    });

    $this->post('saveUserAddress', function ($req, $res) {
        $usMod = new UsuarioModel();
    
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $usMod->saveUserAddress(
                    $req->getParsedBody()
                )
            )
        );
    });

    $this->get('getAdresses/{usuario_id}', function ($req, $res, $args) {
        $usMod = new UsuarioModel();
    
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $usMod->getAdresses($args['usuario_id'])
            )
        );
    });

    $this->get('getMainAdress/{usuario_id}', function ($req, $res, $args) {
        $usMod = new UsuarioModel();
    
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $usMod->getMainAdress($args['usuario_id'])
            )
        );
    });

    $this->get('setAddress/{usuario_id}/{direccion_id}', function ($req, $res, $args) {
        $usMod = new UsuarioModel();
    
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $usMod->setAddress($args['usuario_id'],$args['direccion_id'])
            )
        );
    });


    
    
});