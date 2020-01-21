<?php
namespace App\Libraries;

use Log;
use File;
use Storage;

class ImageUploader
{
    private $file;

    public function __construct($file = null)
    {
        $this->setFile($file);
    }

    public function setFile($file)
    {
        $this->file = $file;
    }

    public function storeImage($uploadSavePath = '/uploads/')
    {
        try {
            $img = $this->file;
            if (empty($img)) {
                throw new \Exception("File is empty");
            }

            // check extensions
            $tipefile = $img->getClientOriginalExtension();
            if (!in_array($tipefile, ['jpg' ,'jpeg','png'])) {
                throw new \Exception('Bukan file gambar yang diijinkan');
            }

            // save uploaded file to storage
            $storage_put_file_path = Storage::putFileAs(
                'uploads',
                $img,
                date("Ymd_His")."_".$img->getClientOriginalName()
            );
            $storage_saved_path = storage_path("app/".$storage_put_file_path);

            // check directory on public uploads path
            $destinationPath = public_path($uploadSavePath);
            if (!File::exists($destinationPath)) {
                $new_dir = File::makeDirectory($destinationPath, 0777, true, true);
            }

            $filename = date("Ymd_His")."_".$img->getClientOriginalName();

            // move uploaded file from storage to public path
            $image_rename_path = $destinationPath.$filename;
            rename($storage_saved_path, $image_rename_path);

            $image_saved_path = $uploadSavePath.$filename;

            return $image_saved_path;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }
    }

    public function storeImageBase64($base64, $filename, $path = '')
    {
        try {
            $exploded_comma = explode(",", $base64);

            $exploded_tt_comma = explode(";", $exploded_comma[0]);
            $exploded_slash = explode("/", $exploded_tt_comma[0]);
            $filetype = $exploded_slash[1];

            $cleaned = isset($exploded_comma[1]) ? $exploded_comma[1] : $exploded_comma[0];
            $cleaned = str_replace(' ', '+', $cleaned);

            $filename .= ".$filetype";

            if (!empty($path)) {
                $filename = $path.$filename;
            }

            Storage::disk('foto')->put($filename, base64_decode($cleaned));
            $url = Storage::disk('foto')->url($filename);

            return $url;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }
    }
}
