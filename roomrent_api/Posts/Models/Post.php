<?php

namespace Roomrent\Posts\Models;

use Illuminate\Database\Eloquent\Model;
use Roomrent\Images\Models\Image;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @SWG\Definition(
 *    required={"title", "post_description", "location",
 *      "latitude", "longitude", "price", "no_of_rooms", "offer_or_ask"},
 *    type="object",
 *    definition="Post",
 *    @SWG\Property(property="title", type="string", example="Title"),
 *    @SWG\Property(property="post_description", type="string",
 *      example="Room available at Patan"),
 *    @SWG\Property(property="location", type="string", example="Patan"),
 *    @SWG\Property(property="latitude", type="integer", example="000.00000000"),
 *    @SWG\Property(property="longitude", type="integer", example="000.00000000"),
 *    @SWG\Property(property="price", type="integer", example="3000"),
 *    @SWG\Property(property="no_of_rooms", type="integer", example="2"),
 *    @SWG\Property(property="offer_or_ask", type="integer", example="1")
 *)
 */
class Post extends Model
{
    use SoftDeletes;
    /**
     * mass assignable property
     * @var array
     */
    protected $fillable  = [
    'post_description','location','latitude','longitude','price',
    'no_of_rooms','title','user_id' , 'offer_or_ask'
    ];

    /**
     * properties hidden 
     * 
     * @var array
     */
    protected $hidden = [
     'deleted_at', 'user_id'
    ];

    protected $dates = ['deleted_at'];

    /**
     * Gets the use belonged to the particular post
     * 
     * @return relationship returns user as property of the model
     */
    public function user()
    {
        return $this->belongsTo('Roomrent\User\Models\User');
    }

    /**
     * Gets the images of offer
     *  
     * @return Array
     */
    public function images()
    {
        $images   = Image::wherePostId($this->id);
        return $images;

    }
   
    protected static function boot()
    {
        parent::boot();

        Post::deleting(function($post)
        {
            $post->images()->delete();
        });

        static::addGlobalScope('updated_at', function (Builder $builder) {
            $builder->orderBy('updated_at', 'desc');
        });
    }
}
