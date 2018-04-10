<?php
use App\Model\ProductoModel;

$app->group('/producto/', function () {


    $this->get('searchProducts/{tag}/{usuario_id}', function ($req, $res, $args) {
        $prdMod = new ProductoModel();
		
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $prdMod->searchProducts($args['tag'], $args['usuario_id'])
            )
        );
    });

    $this->get('wineDetail/{producto_id}/{usuario_id}', function ($req, $res, $args) {
        $prdMod = new ProductoModel();
    
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $prdMod->wineDetail($args['producto_id'], $args['usuario_id'])
            )
        );
    });


    
    
});