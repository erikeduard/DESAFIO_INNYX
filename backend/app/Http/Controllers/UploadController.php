<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    /**
     * Handle the file upload request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required | image | max:2048',
        ]);
        if ($request->file('file')->isValid()) {
            $produto_id = $request->input('id');
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $fileName = $produto_id . '_' . $fileName;
            $path = Storage::disk('public')->put($fileName, file_get_contents($file));
            return response()->json(['path' => $path, 'file'=>$fileName], 201);
        }
        return response()->json(['error' => 'Nenhum arquivo enviado'], 400);
    }

    /**
     * Get the image file for download.
     *
     * @param string $file The name of the file to download.
     * @return \Illuminate\Http\Response The response containing the file for download.
     */
    public function getImagem(string $file)
    {
        $path = Storage::disk('public')->path($file);
        if (file_exists($path)) {
            return response()->file($path);
        }
        return response()->json(['error' => 'Arquivo não encontrado'], 404);
    }
}
