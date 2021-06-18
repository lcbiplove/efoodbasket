<?php

namespace App\Models;

/**
 * Example image model
 *
 * PHP version 7.3
 */
class Image 
{
    /**
     * Valid image extensions
     * @var array
     */
    public const DEFAULT_EXTENSION = ['image/jpeg',
        'image/jpg',
        'image/gif',
        'image/png'
    ];

    /**
     * Maximum file size
     * @var int
     */
    public const DEFAULT_SIZE = 2048;

    /**
     * Class constructor
     *
     * @param array $data  Initial property values (optional)
     *
     * @return void
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $lowerKey = strtolower($key);
            $this->$lowerKey = $value;
        };
    }

    /**
     * Saves the images file to media directory
     * 
     * @param array FILES - $_FILES super global
     * @param string file_key_name - File key name from form
     * @param string folder_name - Name of folder to put file in
     * @return string path - File names separated by "||
     */
    public static function save($FILES, $file_key_name, $folder_name)
    {
        $paths = [];
        
        $folder_name = "media/$folder_name";

        if ( !file_exists( $folder_name ) ) {
            mkdir($folder_name, 0777, true);
        } 

        foreach($FILES[$file_key_name]["tmp_name"] as $key=>$tmp_name) {
            $formFileName = $FILES[$file_key_name]["name"][$key];
            $file_tmp = $FILES[$file_key_name]["tmp_name"][$key];

            $ext=pathinfo($formFileName, PATHINFO_EXTENSION);
            $fileNameWithoutExt = pathinfo($formFileName, PATHINFO_FILENAME);

            $final_filename = "$fileNameWithoutExt.$ext";
            $file_counter = 1;
            
            while (file_exists("$folder_name/$final_filename"))
                $final_filename = $fileNameWithoutExt . '_' . $file_counter++ . '.' . $ext;

            move_uploaded_file($file_tmp, "$folder_name/$final_filename");
            array_push($paths, $final_filename);
        }
        return join("|", $paths);
    }

    /**
     * Validate image supplied
     * 
     * @param array FILES - $_FILES auto global
     * @param string file_key_name - name of file from form
     * @param int max_file_count - maximum files number
     * @param int max_file_size - maximum file size in KB
     * @param array valid_ext - array of valid extensions
     * @return mixed true if valid, error string if invalid 
     */
    public static function validateImage($FILES, $file_key_name, $max_file_count, $max_file_size=Image::DEFAULT_SIZE, $valid_ext=Image::DEFAULT_EXTENSION)
    {
        $files_count = count($FILES[$file_key_name]["tmp_name"]);
        $max_file_size = $max_file_size * 1024;

        if($files_count == 0){
            return "Please upload at least an image.";
        }

        if($files_count > $max_file_count){
            return "You can upload maximum of $max_file_count images only.";
        }

        foreach($FILES[$file_key_name]["tmp_name"] as $key=>$tmp_name) {
            $file_size = $FILES[$file_key_name]["size"][$key];
            $file_ext = $FILES[$file_key_name]["type"][$key];

            if($file_size == 0) {
                return "Please upload at least one file.";
            }
            if($file_size > $max_file_size) {
                return "Please upload a file of size less than 2 MB.";
            }
            if((!in_array($file_ext, $valid_ext)) && (!empty($file_ext))){
                return "Please upload valid file image. Only jpg, jpeg and png types are accepted.";
            }
        }
        return true;
    }
}


