<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ProfileResource extends JsonResource
{
    public static $wrap = 'profile';

    protected $following;

    public function __construct($resource, $following=null)
    {
        parent::__construct($resource);
        $this->following = $following;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {



        return [
            'username' => $this->username,
            'bio' => $this->bio,
            'image' => $this->image,
            'following' => $this->following,
        ];
    }
}
