<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Http\Controllers\ResponseController as RC;

class PostController extends RC {

    public function index() {
        $post = Post::all();

        return $this->sendResponse($post, "datos devueltos correctamente", 200);
    }

    public function store(Request $request) {
        try {
            $post = new Post();
            $post->title = $request->title;
            $post->body = $request->body;
            if ($post->save()) {
                return $this->sendResponse("ok", "Post creado correctamente", 200);
            }
        } catch (Exception $ex) {
            return $this->sendError($ex, $ex->getMessage(), 400);
        }
    }

    public function update(Request $request, $id) {
        try {
            $post = Post::findOrFail($id);
            $post->title = $request->title;
            $post->body = $request->body;
            if ($post->save()) {
                return $this->sendResponse("ok", "Post actualizado correctamente", 200);
            }
        } catch (Exception $ex) {
            return $this->sendError($ex, $ex->getMessage(), 400);
        }
    }

    public function destroy($id) {
        try {
            $post = Post::findOrFail($id);
            if ($post->delete()) {
                return $this->sendResponse("ok", "Post borrado correctamente", 200);
            }
        } catch (Exception $ex) {
            return $this->sendError($ex, $ex->getMessage(), 400);
        }
    }

}
