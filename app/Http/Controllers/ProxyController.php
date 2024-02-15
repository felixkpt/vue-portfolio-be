<?php

namespace App\Http\Controllers;

class ProxyController extends Controller
{
    public function index()
    {
        $source = request()->source;

        if (!$source) {
            $content = 'Source not supplied';
        } else {


            try {

                $content = file_get_contents($source);
                if ($content === false) {
                    $content = 'Failed to retrieve content from the source';
                }
            } catch (\Exception $e) {
                // Handle the exception
                $errorMessage = $e->getMessage();
                $content = "<div>Error: $errorMessage</div>";
            }
        }

        return view('proxy', ['content' => $content]);
    }
}
