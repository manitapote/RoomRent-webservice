<?php



class Api 
{
     /**
     *@SWG\Swagger(
     *     basePath="",
     *     schemes={"http"},
     *     @SWG\Info(
     *         version="1.0",
     *         title="Sample API",
     *         @SWG\Contact(name="Hiren Dave", email="hdave10@gmail.com"),
     *     ),
     *   @SWG\Get(
     *     path="/",
     *     description="Returns basic information",
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response=200,
     *         description="Application Overview"
     *     )
     *   )
     * )

     */
     public function index()
 {
              return Response::json(array('success'=>false,'message'=>'This is Basic Info of API'));
 }
}