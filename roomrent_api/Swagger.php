<?php 

 /** 
  * @SWG\Post(
  *     path="/register",
  *     tags={"user"},
  *     summary="create new user",
  *     description="available for new user",
  *     operationId="createUser",
  *     produces={"application/json"},
  *     @SWG\Parameter(
  *         in="formData",
  *         name="email",
  *         format="string",
  *         description="email",
  *         required=true,
  *         type="string"
  *     ),
  *     @SWG\Parameter(
  *         in="formData",
  *         name="username",
  *         description="username",
  *         required=true,
  *         type="string"
  *     ),
  *     @SWG\Parameter(
  *         in="formData",
  *         name="name",
  *         description="Name",
  *         required=false,
  *         type="string"
  *     ),
  *     @SWG\Parameter(
  *         in="formData",
  *         name="password",
  *         description="password",
  *         required=true,
  *         type="string"
  *     ),
  *     @SWG\Parameter(
  *         in="formData",
  *         name="phone",
  *         description="Phone No.",
  *         required=true,
  *         type="integer"
  *     ),
  *     @SWG\Parameter(
  *        in="formData",
  *        name="file",
  *        description="Profile Image",
  *        required=true,
  *        type="file"
  *     ),
  *     @SWG\Response(response="405", description="Invalid inputs")
  * )
  */
    
 /**
  * @SWG\Post(
  *     path="/update",
  *     tags={"user"},
  *     summary="update user profile",
  *     description="available for logged in user",
  *     operationId="updateUser",
  *     consumes={"application/x-www-form-urlencoded"},
  *     produces={"application/json"},
  *     @SWG\Parameter(
  *         in="formData",
  *         name="username",
  *         description="username",
  *         required=true,
  *         type="string"
  *     ),
  *     @SWG\Parameter(
  *         in="formData",
  *         name="name",
  *         description="Name",
  *         required=false,
  *         type="string"
  *     ),
  *     @SWG\Parameter(
  *         in="formData",
  *         name="phone",
  *         description="Phone No.",
  *         required=false,
  *         type="integer"
  *     ),
  *     @SWG\Parameter(
  *         in="formData",
  *         name="file",
  *         description="profile image",
  *         required=false,
  *         type="file"
  *     ),
  *     security={
  *            {"api_key":{}}
  *     },
  *     @SWG\Response(response="405", description="Invalid inputs")
  * )
  */

 /**
  * @SWG\Post(
  *     path="/login",
  *     tags={"user"},
  *     summary="login a user",
  *     description="User must be registered",
  *     operationId="loginUser",
  *     consumes={"application/x-www-form-urlencoded"},
  *     produces={"application/json"},
  *     @SWG\Parameter(
  *         in="formData",
  *         name="identity",
  *         description="username or email",
  *         required=true,
  *         type="string"
  *         ),
  *     @SWG\Parameter(
  *         in="formData",
  *         name="password",
  *         description="password of user",
  *         required=true,
  *         type="string"
  *         ),
  *     @SWG\Parameter(
  *         in="formData",
  *         name="device_type",
  *         description="type of device",
  *         required=true,
  *         type="string"
  *         ),
  *     @SWG\Parameter(
  *         in="formData",
  *         name="device_token",
  *         description="device specific token",
  *         required=true,
  *         type="string"
  *         ),
  *     @SWG\Response(response="400", description="Invalid username or password")
  *)
  */


 /**
  * @SWG\Get(
  *     path="/activate/{token}",
  *     tags={"user"},
  *     summary="activates the user",
  *     description="user need to be registered",
  *     operationId="activate",
  *     produces={"application/json"},
  *     @SWG\Parameter(
  *         in="path",
  *         name="token",
  *         description="activate user with the token",
  *         required=true,
  *         type="string"
  *     ),
  *     @SWG\Response(response="405", description="invalid token")
  *)
  */
 
 /**
  * @SWG\Post(
  *  path="/forgotpassword",
  *  tags={"user"},
  *  summary="mails for new password",
  *  description="only for already registered user",
  *  operationId="mailForgotPassword",
  *  produces={"application/json"},
  *  @SWG\Parameter(
  *      in="formData",
  *      name="email",
  *      description="Email that user registered with",
  *      required=true,
  *      type="string"
  *  ),
  *  @SWG\Response(response="405", description="invalid inputs")
  *)
  */
 
 /**
  * @SWG\Get(
  *     path="/forgotpassword/{token}",
  *     tags={"user"},
  *     summary="check token and display form for password reset",
  *     description="user need to be registered",
  *     operationId="tokenCheckForgotPassword",
  *     produces={"application/json"},
  *     @SWG\Parameter(
  *         in="path",
  *         name="token",
  *         description="check the token for password reset",
  *         required=true,
  *         type="string"
  *     ),
  *     @SWG\Response(response="405", description="invalid token")
  *)
  */

 /**
  * @SWG\Post(
  *  path="/forgotpassword/change",
  *  tags={"user"},
  *  summary="change password of user",
  *  description="only registered user can change the password",
  *  operationId="forgotPasswordChange",
  *  produces={"application/json"},
  *   @SWG\Parameter(
  *      in="formData",
  *      name="email",
  *      description="email",
  *      required=true,
  *      type="string"
  *  ),
  *  @SWG\Parameter(
  *      in="formData",
  *      name="newPassword",
  *      description="new password to be set",
  *      required=true,
  *      type="string"
  *  ),
  *   @SWG\Parameter(
  *      in="formData",
  *      name="newPassword_confirmation",
  *      description="user whose password to be changed",
  *      required=true,
  *      type="string"
  *  ),
  *  @SWG\Response(response="405", description="invalid inputs")
  *)
  */
 
 /**
  * @SWG\Post(
  *  path="/changepassword",
  *  tags={"user"},
  *  summary="change password of loggedin user",
  *  description="only loggedin user can change password",
  *  operationId="changePassword",
  *  produces={"application/json"},
  *   @SWG\Parameter(
  *      in="formData",
  *      name="oldPassword",
  *      description="old password",
  *      required=true,
  *      type="string"
  *  ),
  *  @SWG\Parameter(
  *      in="formData",
  *      name="newPassword",
  *      description="new password to be set",
  *      required=true,
  *      type="string"
  *  ),
  *  security={
  *      {"api_key":{}}
  *  },
  *  @SWG\Response(response="405", description="invalid inputs")
  *)
  */
 
 /**
  * @SWG\Post(
  *     path="/logout",
  *     tags={"user"},
  *     summary="logout",
  *     operationId="logout",
  *     produces={"application/json"},
  *     description="user must be logged in",
  *     parameters={},
  *     security={
  *         {"api_key":{}}
  *     },
  *     @SWG\Response(response="default", description="successfully logout")
  * )
  */
 
 /**
  * @SWG\Get(
  *     path="/post",
  *     tags={"post"},
  *     summary="Post according to query string",
  *     description="gets post for loggedin user",
  *     operationId="getPost",
  *     produces={"application/json"},
  *     @SWG\Parameter(
  *         in="query",
  *         name="offset",
  *         description="gets the data skipping no of offset posts",
  *         required=false,
  *         type="integer"
  *     ),
  *      @SWG\Parameter(
  *         in="query",
  *         name="user",
  *         description="gets the post of user",
  *         required=false,
  *         type="boolean"
  *     ),
  *     @SWG\Parameter(
  *         in="query",
  *         name="offer_or_ask",
  *         description="gets the offer or ask of user",
  *         required=false,
  *         type="integer"
  *     ),
  *     @SWG\Parameter(
  *         in="query",
  *         name="details",
  *         description="either get detailed info or just summary of info",
  *         required=false,
  *         type="boolean"
  *     ),
  *     security={
  *             {"api_key":{}}
  *     },
  *     @SWG\Response(response="405", description="Invalid inputs")
  * )
  */
 
   /**
   * @SWG\Post(
   *     path="/post/create",
   *     tags={"post"},
   *     summary="creates post",
   *     description="creates post for loggedin user",
   *     operationId="setPost",
   *     produces={"application/json"},
   *     @SWG\Parameter(
   *         in="formData",
   *         name="title",
   *         description="title of post",
   *         required=true,
   *         type="string"
   *     ),
   *     @SWG\Parameter(
   *         in="formData",
   *         name="post_description",
   *         description="description of the post",
   *         required=true,
   *         type="string"
   *     ),
   *     @SWG\Parameter(
   *         in="formData",
   *         name="location",
   *         description="Address",
   *         required=true,
   *         type="string"
   *     ),
   *     @SWG\Parameter(
   *         in="formData",
   *         name="latitude",
   *         description="latitude of the location",
   *         required=true,
   *         type="number"
   *     ),
   *    @SWG\Parameter(
   *         in="formData",
   *         name="longitude",
   *         description="longitude of the place",
   *         required=true,
   *         type="number"
   *     ),
   *    @SWG\Parameter(
   *         in="formData",
   *         name="price",
   *         description="price of the post",
   *         required=true,
   *         type="integer"
   *     ),
   *    @SWG\Parameter(
   *         in="formData",
   *         name="no_of_rooms",
   *         description="number of rooms",
   *         required=true,
   *         type="integer"
   *     ),
   *    @SWG\Parameter(
   *         in="formData",
   *         name="offer_or_ask",
   *         description="1 for offer 2 for ask",
   *         required=true,
   *         type="integer"
   *     ),
   *    @SWG\Parameter(
   *         in="formData",
   *         name="file[0]",
   *         description="image file",
   *         required=false,
   *         type="file"
   *     ),
   *     @SWG\Parameter(
   *         in="formData",
   *         name="file[1]",
   *         description="image file",
   *         required=false,
   *         type="file"
   *     ),
   *     security={
   *             {"api_key":{}}
   *      },
   *     @SWG\Response(response="405", description="Invalid inputs")
   * )
   */
  
  /**
  * @SWG\Post(
  *     path="/post/{id}/update",
  *     tags={"post"},
  *     summary="updates post",
  *     description="update post for loggedin user",
  *     operationId="updatePost",
  *     produces={"application/json"},
  *     @SWG\Parameter(
  *         in="formData",
  *         name="title",
  *         description="title of post",
  *         required=true,
  *         type="string"
  *     ),
  *     @SWG\Parameter(
  *         in="formData",
  *         name="post_description",
  *         description="description of the post",
  *         required=true,
  *         type="string"
  *     ),
  *     @SWG\Parameter(
  *         in="formData",
  *         name="location",
  *         description="Address",
  *         required=true,
  *         type="string"
  *     ),
  *     @SWG\Parameter(
  *         in="formData",
  *         name="latitude",
  *         description="latitude of the location",
  *         required=true,
  *         type="number"
  *     ),
  *    @SWG\Parameter(
  *         in="formData",
  *         name="longitude",
  *         description="longitude of the place",
  *         required=true,
  *         type="number"
  *     ),
  *    @SWG\Parameter(
  *         in="formData",
  *         name="price",
  *         description="price of the post",
  *         required=true,
  *         type="integer"
  *     ),
  *    @SWG\Parameter(
  *         in="formData",
  *         name="no_of_rooms",
  *         description="number of rooms",
  *         required=true,
  *         type="integer"
  *     ),
  *    @SWG\Parameter(
  *         in="formData",
  *         name="offer_or_ask",
  *         description="1 for offer 2 for ask",
  *         required=true,
  *         type="integer"
  *     ),
  *    @SWG\Parameter(
  *         in="formData",
  *         name="file[0]",
  *         description="image file",
  *         required=false,
  *         type="file"
  *     ),
  *     @SWG\Parameter(
  *         in="formData",
  *         name="file[1]",
  *         description="image file",
  *         required=false,
  *         type="file"
  *     ),
  *     @SWG\Parameter(
  *         in="path",
  *         name="id",
  *         description="post id",
  *         required=true,
  *         type="string"
  *     ),
  *     security={
  *             {"api_key":{}}
  *      },
  *     @SWG\Response(response="405", description="Invalid inputs")
  * )
  */
 
 /**
 * @SWG\Post(
 *     path="/postbylocation",
 *     tags={"post"},
 *     summary="post near the given location",
 *     description="gets post around a certain distance",
 *     operationId="getPostByLocation",
 *     produces={"application/json"},
 *     @SWG\Parameter(
 *         in="formData",
 *         name="latitude",
 *         description="latitude of a place",
 *         required=true,
 *         type="number"
 *     ),
 *     @SWG\Parameter(
 *         in="formData",
 *         name="longitude",
 *         description="longitude of a place",
 *         required=true,
 *         type="number"
 *     ),
 *     @SWG\Parameter(
 *         in="formData",
 *         name="offset",
 *         description="gets post after offset value",
 *         type="number",
 *         required=false
 *     ),
 *      @SWG\Parameter(
 *         in="formData",
 *         name="distance",
 *         description="distance to get the post within given distance",
 *         type="number"
 *     ),
 *     security={
 *             {"api_key":{}}
 *      },
 *     @SWG\Response(response="405", description="Invalid inputs")
 * )
 */
 

 /**
   * @SWG\Post(
   *     path="/matchingposts",
   *     tags={"post"},
   *     summary="gets criteria matching post",
   *     description="gets post matching criteria for loggedin user",
   *     operationId="criteriaMachingPost",
   *     produces={"application/json"},
   *     @SWG\Parameter(
   *         in="formData",
   *         name="latitude",
   *         description="latitude of the location",
   *         required=true,
   *         type="number"
   *     ),
   *     @SWG\Parameter(
   *         in="formData",
   *         name="longitude",
   *         description="longitude of the place",
   *         required=true,
   *         type="number"
   *     ),
   *     @SWG\Parameter(
   *         in="formData",
   *         name="price",
   *         description="price of the post",
   *         required=true,
   *         type="integer"
   *     ),
   *     @SWG\Parameter(
   *         in="formData",
   *         name="offer_or_ask",
   *         description="1 for offer 2 for ask",
   *         required=true,
   *         type="integer"
   *     ),
   *      @SWG\Parameter(
   *         in="formData",
   *         name="distance",
   *         description="distance to get post within the given distance",
   *         required=false,
   *         type="integer"
   *     ),
   *     security={
   *             {"api_key":{}}
   *      },
   *     @SWG\Response(response="405", description="Invalid inputs")
   * )
   */
 
 /**
  *  @SWG\Get(
  *     path="/user/{id}",
  *     tags={"user"},
  *     summary="gets user of given id",
  *     description="user of particuler id",
  *     operationId="getParticulerUser",
  *     produces={"application/json"},
  *     @SWG\Parameter(
  *         in="path",
  *         name="id",
  *         description="user id",
  *         required=true,
  *         type="number"
  *     ),
  *     security={
  *             {"api_key":{}}
  *      },
  *     @SWG\Response(response="405", description="Invalid inputs")
  * )
  */
 

 /**
  *  @SWG\Get(
  *     path="/post/{id}",
  *     tags={"post"},
  *     summary="gets post of given id",
  *     description="Post of partuculer id",
  *     operationId="getParticulerPost",
  *     produces={"application/json"},
  *     @SWG\Parameter(
  *         in="path",
  *         name="id",
  *         description="post id",
  *         required=true,
  *         type="number"
  *     ),
  *     security={
  *             {"api_key":{}}
  *      },
  *     @SWG\Response(response="405", description="Invalid inputs")
  * )
  */
 

 /**
  *  @SWG\Get(
  *     path="/fire",
  *     tags={"post"},
  *     summary="fires notifications",
  *     description="gets notification",
  *     operationId="fire",
  *     produces={"application/json"},
  *     parameters={},
  *     @SWG\Response(response="405", description="Invalid inputs")
  * )
  */
 
 /**
  *  @SWG\Post(
  *     path="/post/delete",
  *     tags={"post"},
  *     summary="delets post",
  *     description="Delets the post matching the id",
  *     operationId="deletePosts",
  *     produces={"application/json"},
  *     @SWG\Parameter(
  *         in="body",
  *         name="id",
  *         description="post id",
  *         required=true,
  *         type="array",
  *         @SWG\Schema(
  *           type="string")
  *     ),
  *     security={
  *             {"api_key":{}}
  *      },
  *     @SWG\Response(response="405", description="
  *       {
  *         code : 0071,
  *         message : Posts(s) not found
  *       },
  *       {
  *         code : 0000,
  *         message : Error Occured
  *       }"),
  *     @SWG\Response(response="200", description="
  *       {
  *         code : 0001,
  *         message : Successfully deleted
  *       }")
  * )
  */
