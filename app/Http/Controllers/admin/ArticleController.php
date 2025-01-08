<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::orderBy('created_at', 'DESC')->get();

        return response()->json([
            'status' => true,
            'data' => $articles
        ]);
    }

    public function store(Request $request)
    {
        $request->merge(['slug' => Str::slug($request->slug)]);

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'slug' => 'required|unique:articles,slug',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $article = new Article();
        $article->title = $request->title;
        $article->slug = Str::slug($request->slug);
        $article->author = $request->author;
        $article->content = $request->content;
        $article->status = $request->status;

        $article->save();


        // Save Temp Image Here
        if ($request->imageId > 0) {

            $tempImage =  TempImage::find($request->imageId);

            if ($tempImage != null) {

                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $fileName = strtotime('now') . $article->id . '.' . $ext;

                // Create small thumbnail here
                $sourcePath = public_path('uploads/temp/' . $tempImage->name);
                $destPath = public_path('uploads/articles/small/' . $fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->coverDown(500, 600);
                $image->save($destPath);

                // Create large thumbnail here
                $destPath = public_path('uploads/articles/large/' . $fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->scaleDown(1200);
                $image->save($destPath);

                $article->image = $fileName;
                $article->save();
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Article Added Successfully'
        ]);
    }

    public function show(string $id)
    {

        $article = Article::findOrFail($id);

        if ($article == null) {
            return response()->json([
                'status' => false,
                'errors' => 'Article Not Found'
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $article
        ]);
    }

    public function update(Request $request, string $id)
    {

        $article = Article::findOrFail($id);

        $request->merge(['slug' => Str::slug($request->slug)]);

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'slug' => 'required|unique:articles,slug,' . $id . ',id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $article->title = $request->title;
        $article->slug = Str::slug($request->slug);
        $article->author = $request->author;
        $article->content = $request->content;
        $article->status = $request->status;

        $article->save();


        // Save Temp Image Here
        if ($request->imageId > 0) {

            $oldImage = $article->image;
            $tempImage =  TempImage::find($request->imageId);


            if ($tempImage != null) {

                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $fileName = strtotime('now') . $article->id . '.' . $ext;

                // Create small thumbnail here
                $sourcePath = public_path('uploads/temp/' . $tempImage->name);
                $destPath = public_path('uploads/articles/small/' . $fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->coverDown(500, 600);
                $image->save($destPath);

                // Create large thumbnail here
                $destPath = public_path('uploads/articles/large/' . $fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->scaleDown(1200);
                $image->save($destPath);

                $article->image = $fileName;
                $article->save();

                if ($oldImage != '') {
                    File::delete(public_path('uploads/articles/large/' . $oldImage));
                    File::delete(public_path('uploads/articles/small/' . $oldImage));
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Article Updated Successfully'
        ]);
    }

    public function destroy(string $id)
    {

        $article = Article::findOrFail($id);

        $oldImage = $article->image;

        if ($article == null) {
            return response()->json([
                'status' => false,
                'errors' => 'Article Not Found'
            ]);
        }

        if ($oldImage != null) {

            File::delete(public_path('uploads/articles/large/' . $oldImage));
            File::delete(public_path('uploads/articles/small/' . $oldImage));
        }

        $article->delete();

        return response()->json([
            'status' => true,
            'message' => "Article Deleted Successfully"
        ]);
    }
}
