<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

trait ImageTrait {
    public function upload($img, $path, $thumb, $oldImg = null)
    {
        // check directory
        if (!File::isDirectory($path))
        {
            // create new if not exist
            File::makeDirectory($path, 0777, true, true);
            File::makeDirectory($thumb, 0777, true, true);
        }

        $imageName  = time().'.'.uniqid().'.'.$img->getClientOriginalExtension();

        $image      = Image::make($img->getRealPath());
        $image->save($path.'/'.$imageName);
        $image->resize(180, 180, function($cons)
            {
                $cons->aspectRatio();
            })->save($thumb.'/'.$imageName);

        if (!is_null($oldImg))
        {
            File::delete($path.'/'.$oldImg);
            File::delete($thumb.'/'.$oldImg);
        }

        return $imageName;
    }
}
