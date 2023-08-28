<?php

namespace App\TestCenter\Traits;

use App\TestCenter\Utilities\Generators;
use Illuminate\Support\Facades\Storage;


trait ImageTrait{

    protected $imageUploadParams = [
        "image",
        "test_id"
    ];

    private function getImage( String $base64 )
    {
        $image = str_replace( 'data:image/png;base64,', '', $base64 );
        $image = str_replace( 'data:image/jpeg;base64,', '', $image );
        $image = str_replace( ' ', '+', $image );
        return base64_decode( $image );
    }

    private function getPDFfromBase64( String $base64 )
    {
        return base64_decode( $base64 );
    }

    public function uploadPDF( $file, $file_name )
    {
        Storage::disk('s3.training-resources')->put( $file_name, $file );
        return Storage::disk('s3.training-resources')->url( $file_name );
    }

    public function uploadImageS3( $file_name , $file )
    {
        Storage::disk('s3.sp-test-photos')->put( $file_name, $file );
        return Storage::disk('s3.sp-test-photos')->url( $file_name );
    }

    public function deletePDF( $file_name )
    {
        return Storage::disk('s3.training-resources')->delete( $file_name );
    }
}