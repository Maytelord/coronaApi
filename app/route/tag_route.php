<?php
use App\Model\TagModel;

$app->group('/tag/', function () {


    $this->get('searchTags/{tag}', function ($req, $res, $args) {
        $tagMod = new TagModel();
		
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $tagMod->searchTags($args['tag'])
            )
        );
    });


    
    
});