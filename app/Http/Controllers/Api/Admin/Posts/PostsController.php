<?php

namespace App\Http\Controllers\Api\Admin\Posts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Repositories\SearchRepo;
use Illuminate\Support\Facades\Schema;
use App\Http\Traits\ControllerTrait;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PostsController extends Controller
{

    /**
     *  Controller Trait
     */
    use ControllerTrait;

    /**
     * return post's index view
     */
    public function index()
    {
        $posts = Post::paginate();

        return response(['message' => 'success', 'data' => $posts]);
    }

    /**
     * store post
     */
    public function store()
    {

        request()->validate([
            'title' => 'required|unique:posts,title,' . request()->id . ',_id'
        ]);

        $data = \request()->all();

        if (request()->display_time)
            $data['display_time'] = Carbon::parse(request()->display_time)->format('Y-m-d H:i:s');

        $data['slug'] = Str::slug($data['title']);

        if (!isset($data['user_id'])) {
            if (Schema::hasColumn('posts', 'user_id'))
                $data['user_id'] = currentUser()->id;
        }

        if (\request()->id) {
            $action = "updated";
        } else {
            $action = "saved";
            $data['status'] = 'published';
        }

        $res = Post::updateOrCreate(['_id' => request()->id ?? str()->random(20)], $data);
        return response(['type' => 'success', 'message' => 'Post ' . $action . ' successfully', 'data' => $res], 201);
    }

    /**
     * return post values
     */
    public function listPosts()
    {
        $posts = Post::where([]);

        if (\request('all')) {
            if (Schema::hasColumn('posts', 'status')) return $posts->where('status', 1)->orWhereNull('status')->get();
            else return $posts->get();
        }

        return SearchRepo::of($posts)
            ->addColumn('action', function ($post) {
                $str = '';
                $json = json_encode($post);
                $str .= '<a href="javascript:void" data-model="' . htmlentities($json, ENT_QUOTES, 'UTF-8') . '" onclick="prepareEdit(this,\'post_modal\');" class="btn badge btn-info btn-sm"><i class="fa fa-edit"></i> Edit</a>';
                //    $str .= '&nbsp;&nbsp;<a href="javascript:void" onclick="deleteItem(\''.url(request()->user()->role.'/posts/delete').'\',\''.$post->id.'\');" class="btn badge btn-outline-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>';
                return $str;
            })->make();
    }

    function show($id)
    {

        $res = Post::find($id);
        return response(['type' => 'success', 'message' => 'successfully', 'data' => $res], 200);
    }

    /**
     * change post status
     */
    public function changeStatus($id)
    {
        $post = Post::findOrFail($id);
        $state = $post->status == 'published' ? 'Deactivated' : 'Activated';
        $post->status = $post->status == 'published' ? 'draft' : 'published';
        $post->save();
        return response(['type' => 'success', 'message' => 'About #' . $post->id . ' has been ' . $state]);
    }

    /**
     * delete post
     */
    public function destroyPost($post_id)
    {
        $post = Post::findOrFail($post_id);
        $post->delete();
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Post deleted successfully']);
    }
}
