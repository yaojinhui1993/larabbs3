<?php

namespace App\Http\Controllers\Api;

use App\Models\Link;
use App\Http\Controllers\Controller;
use App\Http\Resources\LinkResource;

class LinksController extends Controller
{
    public function index(Link $link)
    {
        $links = $link->getAllCached();

        LinkResource::wrap('data');

        return LinkResource::collection($links);
    }
}
