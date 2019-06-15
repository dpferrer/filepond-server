<?php

namespace Itsdp\FilepondServer\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Itsdp\FilepondServer\Filepond;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class FilepondController extends BaseController
{

    /**
     * @var Filepond
     */
    private $filepond;

    public function __construct(Filepond $filepond)
    {
        $this->filepond = $filepond;
    }

    /**
     * Uploads the file to the temporary directory
     * and returns an encrypted path to the file
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        if(! Schema::hasTable('filepond_temp')){
            DB::unprepared(file_get_contents(__DIR__.'/../../../database/filepond_temp.txt'));
        }
        if (! Storage::disk('upload')->exists('filepond/temp')) {
            Storage::disk('upload')->makeDirectory('filepond/temp');
        }
        $files = $request->file('file');
        foreach ($files as $file) {

            // For Linux
            // $filePath = tempnam(config('filepond.temporary_files_path'), "laravel-filepond");
           
            // For Windows
            $filePath = config('filepond.temporary_files_path/').'filepond-'.uniqid().'.'.$file->getClientOriginalExtension();
           
            $filePathParts = pathinfo($filePath);

            /*
                Custom
            */
            $extension = $file->getClientOriginalExtension();
            $fileName = $filePathParts['basename'];
            Storage::disk('upload')->putFileAs('filepond/temp', $file, $fileName);
            $timestamp = Carbon::now()->toDateTimeString();
            DB::table('filepond_temp')->insert([
                'user_id' => '0',
                'file_name' => $fileName,
                'extension' => $extension,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ]);
            /*
                -/END
            */

            // Linux
            // if(!$file->move($filePathParts['dirname'], $filePathParts['basename'])) {
            //     return Response::make('Could not save file', 500);
            // }

            return Response::make($this->filepond->getServerIdFromPath($filePath), 200);
        }
    }

    /**
     * Takes the given encrypted filepath and deletes
     * it if it hasn't been tampered with
     *
     * @param Request $request
     * @return mixed
     */
    public function delete(Request $request) {

        $filePath = $this->filepond->getPathFromServerId($request->getContent());
        
        /*
            Custom
        */
        $file = basename($filePath);
        /*$file_from_db = DB::table('filepond_temp')->where('file_name',$file)->get();
        foreach ($file_from_db as $file_data) {
            Storage::disk('upload')->delete('filepond/temp/'.$file_data->file_name.'.'.$file_data->extension);
        }*/
        DB::table('filepond_temp')->where('file_name',$file)->delete();
        Storage::disk('upload')->delete('filepond/temp/'.$file);
        
        /*
            -/END
        */
        
        // Linux
        // if(unlink($filePath)) {
        //     return Response::make('', 200);
        // } else {
        //     return Response::make('', 500);
        // }
    }
}