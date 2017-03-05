<?php
// ver1.2

/**
 * Convert linear array to comma separated string
 * @param $array
 * @return string
 */
function array_to_string($array)
{
    $str = '';
    if($array)
    foreach($array as $word) {
        $str .= $seporator . addslashes($word);
        $seporator = ',';
    }
    return $str;
}


/**
 * Convert comma separated string to liner array
 * @param $array
 * @return array()
 */
function string_to_array($array)
{
    $arr = explode(',', $array);
    foreach($arr as $item)
        $result[$item] = $item;

    return $result;
}

/**
 * Generate initial random pointer
 * @return initial random value
 */
function make_seed()
{
	list($usec, $sec) = explode(' ', microtime());
	return (float) $sec + ((float) $usec * 100000);
}

/**
 * Generate random string
 * @param $min_len
 * @param $max_len
 */
function get_random_string($min_len = 10, $max_len = 20)
{
    $chars = array('q', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o',
    				'p', 'a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 
    				'l', 'z', 'x', 'c', 'v', 'b', 'n', 'm', 'Q', 
    				'W', 'E', 'R', 'T', 'Y', 'U', 'I', 'O', 'P', 
    				'A', 'S', 'D', 'F', 'G', 'H', 'J', 'K', 'L', 
    				'Z', 'X', 'C', 'V', 'B', 'N', 'M', '1', '2', 
    				'3', '4', '5', '6', '7', '8', '9', '0');
    $new_string = '';
    srand(make_seed());
    
    $len = rand($min_len, $max_len);
    for($i = 0; $i < $len; $i++)
        $new_string .= $chars[rand(0, count($chars))];
    
    return $new_string;
}


/**
 * Generate unique file name
 * @param $dir
 * @param $filename
 * @return unique file name
 */
function generate_unique_file_name($dir, $filename)
{
    if (!$filename)
        return;
     
    $extension = pathinfo($filename, PATHINFO_EXTENSION);

    $new_filename = '';
    for (;;)
    {
        $new_filename = get_random_string(8, 8) . '.' . $extension;
        if (!file_exists($dir . '/' . $new_filename))
            break;
    }

    return $new_filename;
}


/**
 * Upload new image
 *
 * @param $field_name - file field name in POST query
 * @param $name - image name
 * @param $alt - image "alt" description
 * @return image id or array of images if multiple upload
 */
function upload_image($field_name, $name = '', $alt = '', $title = '')
{
    if (!isset($_FILES[$field_name])) {
        dump('Error upload file: field "' . $field_name . '" not exist in $_FILES');
        dump($_FILES);
        dump('Back trace:');
        dump(debug_backtrace());
        trigger_error('IMAGE Error');
        exit;
    }

    // if multiple upload images, create image list
    $multiple_upload = false;
    if (is_array($_FILES[$field_name]['tmp_name'])) {
        $multiple_upload = true;
        foreach ($_FILES[$field_name]['tmp_name'] as $index => $tmp_name) {
            $upload_image_list[$index]['tmp_name'] = $tmp_name;
            $upload_image_list[$index]['name'] = $_FILES[$field_name]['name'][$index];
        }
    }
    else
        $upload_image_list[0] = $_FILES[$field_name];

    $list_of_inserted_images = array();
    foreach ($upload_image_list as $image) {
        $tmp_filename = $image['tmp_name'];
        $upload_path = global_conf()['absolute_root_path'] . 'i/';
         
        $new_filename = generate_unique_file_name($upload_path, $image['name']);
        if (!$new_filename) {
            dump("ERROR FILE!: ");
            dump('$field_name = ' . $field_name);
            dump($_FILES);
            trigger_error('IMAGE Error');
            exit;
        }
         
        $rc = move_uploaded_file($tmp_filename, $upload_path . $new_filename);
        if (!$rc) {
            dump('Error: upload file: ' . $_FILES[$field_name]['name'] . ' in directory: ' . $upload_path);
            dump('$_FILES: ');
            dump($_FILES);
            dump('Back trace:');
            dump(debug_backtrace());
            trigger_error('IMAGE Error');
            exit;
        }
        /*dump('cp ' . $tmp_filename . ' ' . $upload_path . $new_filename);
         system('cp ' . $tmp_filename . ' ' . $upload_path . $new_filename);
         */
         
        list($img_width, $img_height) = getimagesize($upload_path . $new_filename);
         
        $img_id = db()->insert('images', array('name' => $name,
	                                         'alt' => $alt,
	                                         'title' => $title,
	                                         'enable' => '1',
	                                         'width' => $img_width,
	                                         'height' => $img_height,
	                                         'original_filename' => $image['name'],
	                                         'files' => $new_filename));
         
        $list_of_inserted_images[] = $img_id;
    }

    if ($multiple_upload)
        return $list_of_inserted_images;

    return $img_id;
}

/**
 * Upload new image by URL
 *
 * @param $image_url - full URL to image file
 * @param $name - image name
 * @param $alt - image "alt" description
 * @return image id
 */
function download_image($image_url, $name = '', $alt = '', $title = '')
{
    global $_CONFIG;
     
    $reply = file_get_contents($image_url);
    if (!$reply)
        return 0;

    $upload_path = global_conf()['absolute_root_path'] . 'i/';
    $new_filename = generate_unique_file_name($upload_path, $image_url);
    if (!$new_filename) {
        dump("ERROR FILE!: ");
        dump('$image_url = ' . $image_url);
        trigger_error('IMAGE Error');
        exit;
    }

    $rc = file_put_contents($upload_path . $new_filename, $reply);
    if (!$rc) {
        dump("ERROR FILE!: ");
        dump('$image_url = ' . $image_url);
        trigger_error('IMAGE Error: Permission denyed');
        exit;
    }

    list($img_width, $img_height) = getimagesize($upload_path . $new_filename);

    $img_id = db()->insert('images', array('name' => $name,
                                         'alt' => $alt,
                                         'title' => $title,
                                         'enable' => '1',
                                         'width' => $img_width,
                                         'height' => $img_height,
                                         'original_filename' => $image_url,
                                         'files' => $new_filename));

    return $img_id;
}

 
/**
 * Определить по какой стороне необходимо осуществить изменение размера
 * «адаютс€ размеры окна оба или только одна сторона.
 * ≈сли заданы оба размера окна, то изображение подгон€етс€ так чтобы заполнить собою все окно,
 * предполагаетс€ что выступающе сачти изображени€ перекруютс€ свойствами <div>
 * @param $need_width - ширина окна дл€ изображени€
 * @param $need_height - высота окна дл€ изображени€
 * @param $curr_width - ширина изображени€
 * @param $curr_height - высота изображени€
 * @return ¬озвращает строку:
 'none' - если изменение размера не требуетс€
 'width' - если изменение размера требуетс€ пропорционально по ширине окн
 'height' - если изменение размера требуетс€ пропорционально по высоте окн
 */
function get_resize_mode($need_width, $need_height, $curr_width, $curr_height)
{
    if (!$need_width && !$need_height)
        return 'none';

    // если указаны оба размера, то предполагаетс€ что изображение должно заполнить указанное окно на 100%
    // выпирающие части изображени€ должны перекрыватьс€ с помощью div owerflow:hidden
    // подгонка размера изображени€ по его минимальной стороне к размеру окна
    if ($need_width && $need_height) {
        if (($curr_width <= $need_width) || ($curr_height <= $need_height))
            return 'none';

        if ($curr_width > $curr_height)
            return 'height';

        if ($curr_width < $curr_height)
            return 'width';

        if ($curr_width == $curr_height) {
            if ($need_width < $need_height)
            return 'height';

            if ($need_width > $need_height)
            return 'width';

            if ($need_width == $need_height)
            return 'none';
        }
    }

    if ($need_width && ($curr_width > $need_width))
        return 'width';

    if ($need_height && ($curr_height > $need_height))
        return 'height';

    return 'none';
}


/**
 * Get image info by id
 * @param $id
 * @param $width - create thumbnail image by entered width
 *      if $width = 0 then no create thumbnail image
 * @param $height - create thumbnail image by entered height
 *      if $height = 0 then no create thumbnail image
 * @return image info array
 */
function get_image($id, $width = 0, $height = 0)
{

    $item = db()->query('SELECT * FROM images WHERE id = ' . $id);
    $image = $item[0];
    if (!$image)
        return false;
    $f = explode(',', $image['files']);
    $files = array();
    foreach ($f as $file)
        $files[] = trim($file);
    $image['full_filename'] = global_conf()['absolute_root_path'] . 'i/' . $files[0];
    $image['url'] = global_conf()['http_root_path'] . 'i/' . $files[0];
    $imag['filename'] = $files[0];
    $image['files'] = $files;
    $resize_mode = get_resize_mode($width, $height, $image['width'], $image['height']);
    if ($resize_mode == 'none')
        return $image;

    $path_parts = pathinfo($files[0]);
    $extension = $path_parts['extension'];
    list($filename) = explode('.', $path_parts['basename']);

    switch ($resize_mode) {
    case 'width':
        $thumbnail_filename = $filename . 'w' . $width . '.' . $extension;
        break;

    case 'height':
        $thumbnail_filename = $filename . 'h' . $height . '.' . $extension;
        break;
    }

    if (!file_exists(global_conf()['absolute_root_path'] . '/i/' . $thumbnail_filename)) {
        copy($image['full_filename'], global_conf()['absolute_root_path'] . '/i/' . $thumbnail_filename);
        chmod(global_conf()['absolute_root_path'] . 'i/' . $thumbnail_filename, 0666);
        if ($resize_mode == 'width')
            resize_img($thumbnail_filename, global_conf()['absolute_root_path'] . 'i/', $width, 0);

        else
            resize_img($thumbnail_filename, global_conf()['absolute_root_path'] . 'i/', 0, $height);

        $image['files'][] = $thumbnail_filename;
        db()->update('images', $id, array('files' => array_to_string($image['files'])));
    }

    $image['url'] = global_conf()['http_root_path'] . 'i/' . $thumbnail_filename;
    $image['full_filename'] = global_conf()['absolute_root_path'] . 'i/' . $thumbnail_filename;
    $image['filename'] = $thumbnail_filename;

    return $image;
}


/**
 * Delete image
 * @param $img_id
 */
function delete_image($img_id)
{
    if (!$img_id)
        return false;
    $img = get_image($img_id);
    if ($img['files'])
        foreach ($img['files'] as $file)
            @unlink(global_conf()['absolute_root_path'] . 'i/' . $file);
     
    return (db()->query('DELETE FROM images WHERE id = ' . $img_id));
}




/**
 * add image to object
 * @param $obj_type
 * @param $obj_id
 * @param $img_id
 */
function add_image_to_object($obj_type, $obj_id, $img_id)
{
    /* $row = db_get_item_by_query('SELECT `order` FROM object_images WHERE obj_type = "' .
     strtok($obj_type, " ") . '" AND obj_id = ' . (int)$obj_id .
     ' ORDER BY `order` DESC');

     $last_order = $row['order'] ? $row['order'] : 0;*/

    $r= db()->insert('object_images', array(
                                       'obj_type' => strtok($obj_type, " "),
                                       'obj_id' => (int)$obj_id,
                                       'img_id' => (int)$img_id));
    return $r;
}

/**
 * delete image from object
 * @param $obj_type
 * @param $obj_id
 */
function del_image_from_object($obj_type, $obj_id, $img_id)
{
    db()->query('DELETE FROM object_images WHERE obj_type = "' . strtok($obj_type, " ") .
                 '" AND obj_id = ' . (int)$obj_id . ' AND img_id = ' . (int)$img_id);

    delete_image($img_id);
}

/**
 * delete all images by object
 * @param $obj_type
 * @param $obj_id
 */
function del_images_object($obj_type, $obj_id)
{
    $rows = db()->query('SELECT img_id FROM object_images WHERE obj_type = "' . strtok($obj_type, " ") .
                                     '" AND obj_id = ' . (int)$obj_id);
    if (!$rows)
        return false;

    foreach ($rows as $row)
        delete_image($row['img_id']);

    db()->query('DELETE FROM object_images WHERE obj_type = "' . strtok($obj_type, " ") .
                 '" AND obj_id = ' . (int)$obj_id);

    return true;
}


/**
 * get several sizes by one image
 * @param $img_id - image ID
 * @param $image_sizes - array of image sizes
 *        (
 *            ['big'] => array('w' => 600),
 *            ['mini'] => array('h' => 150),
 *        )
 *
 * @return Array
 *        (
 *            ['id'] => 22,
 *            ['img_id'] => 105,
 *            ['name'] => 'image name',
 *            ['alt'] => 'image alt',
 *            ['url'] => 'original url',
 *            ['url_mini'] => 'mini url',
 *            ['url_big'] => 'big url',
 *            ['url_xxx'] => 'xxx url',
 *            ['file_mini'] => 'mini file name',
 *            ['file_big'] => 'big file name',
 *            ['file_xxx'] => 'xxx file name',
 *        )
 */
function get_images($img_id, $image_sizes)
{
    if ($image_sizes)
    foreach ($image_sizes as $size_name => $size) {
        $img = get_image($img_id, $size['w'], $size['h']);
        $image['url_' . $size_name] = $img['url'];
        $image['file_' . $size_name] = $img['filename'];
    }

    if (!$img)
        $img = get_image($img_id);
     
    $image['img_id'] = $img['id'];
    $image['name'] = $img['name'];
    $image['alt'] = $img['alt'];
    $image['title'] = $img['title'];
    $image['url'] = $img['url'];

    return $image;
}



/**
 * get list images by object id
 * @param $obj_type
 * @param $obj_id
 * @param $image_sizes - see get_images()
 * @param $order - 'ASC' or 'DESC'
 * @return list of arrays by get_images()
 */
function get_object_images($obj_type, $obj_id, $image_sizes = array(), $order = 'ASC')
{
    $rows = db_get_list_by_query('SELECT * FROM object_images WHERE obj_type = "' . 
                                strtok($obj_type, " ") . '" AND obj_id = ' . (int)$obj_id . 
                                ' ORDER by `order` ' . strtok($order, " "));
    if (!$rows)
        return;


    $images = array();
    foreach ($rows as $id => $row) {
        $images[$id] = get_images($row['img_id'], $image_sizes);
        $images[$id]['id'] = $row['id'];
    }

    $images = add_prev_next($images);
    return $images;
}


/**
 * get first image by object id
 * @param $obj_type
 * @param $obj_id
 * @param $image_sizes - see get_images()
 * @param $order - 'ASC' or 'DESC'
 * @return Array of get_images()
 */
function get_first_object_image($obj_type, $obj_id, $image_sizes = array())
{
    $query = 'SELECT * FROM object_images WHERE obj_type = "' . strtok($obj_type, " ") .
                                    '" AND obj_id = ' . (int)$obj_id;
    $row = db()->query( $query);
    if (!$row)
        return;
     
    $image = get_images($row[0]['img_id'], $image_sizes);
    $image['id'] = $row[0]['id'];

    return $image;
}


function generate_image($source, $destination, $w, $h, $action_type = '',
                         $filetype = '', $string = '', $jpeg_quality = '80') // —оздает изображение
{
    $size = getimagesize ($source);
    if (!$filetype)
        $filetype = $size['mime'];

    if ((($filetype == 'image/jpeg' OR $filetype == 'image/pjpeg') OR $filetype == 'image/jpg'))
        $src = imagecreatefromjpeg ($source);
    else {
        if ($filetype == 'image/gif')
            $src = imagecreatefromgif ($source);
        else
            if ($filetype == 'image/x-png' OR $filetype == 'image/png')
                $src = imagecreatefrompng ($source);
            else
        return 0;
    }

    $dest = imagecreatetruecolor ($w, $h);
    if (($src AND $dest)) {
        $sh = $size[1];
        $sw = $size[0];
        if ($action_type == 'resize')
            imagecopyresampled ($dest, $src, 0, 0, 0, 0, $w, $h, $sw, $sh);
        else {
            if ($action_type == 'square') {
                if ($sh < $sw) {
                    $tmp = imagecreatetruecolor ($sh, $sh);
                    imagecopyresampled ($dest, $src, 0, 0, 0, 0, $w, $h, $sh, $sh);
                }
                else {
                    if ($sw < $sh) {
                        $tmp = imagecreatetruecolor ($sw, $sw);
                        imagecopyresampled ($dest, $src, 0, 0, 0, 0, $w, $h, $sw, $sw);
                    }
                    else
                        imagecopyresampled ($dest, $src, 0, 0, 0, 0, $w, $h, $sw, $sh);
                }
                @imagedestroy ($tmp);
            }
            else
                imagecopyresampled ($dest, $src, 0, 0, 0, 0, $w, $h, $w, $h);
        }

        if ($string) {
            $bg = imagecolorallocatealpha ($dest, 255, 255, 255, 200);
            $text_color = imagecolorallocate ($dest, 255, 255, 255);
            imagefilledrectangle ($dest, 0, 0, 6 + strlen ($string) * 8, 23, $bg);
            imagestring ($dest, 3, 5, 5, $string, $text_color);
        }

        if ((($filetype == 'image/jpeg' OR $filetype == 'image/pjpeg') OR $filetype == 'image/jpg'))
            imagejpeg ($dest, $destination, $jpeg_quality);
        else
            if ($filetype == 'image/gif')
                imagegif ($dest, $destination);
            else
                if ($filetype == 'image/x-png' OR $filetype == 'image/png')
                    imagepng ($dest, $destination);

        @imagedestroy ($src);
        @imagedestroy ($dest);
        return TRUE;
    }
    return FALSE;
}

/**
 * Change image size
 * @param $imagename
 * @param $path
 * @param $max_width
 * @param $max_height
 * @param $filetype
 * @param $string
 * @param $jpeg_quality
 */
function resize_img($imagename, $path, $max_width, $max_height, $filetype = '', $string = NULL, $jpeg_quality = 66)
{
    if (!file_exists($path . $imagename))
        return false;

    list ($w, $h) = getimagesize($path . $imagename);
     
    if ($max_width && ($max_width < $w)) {
        $dw = $max_width;
        $dh = (int)$h * ($max_width / $w);
    }
    else 
        if ($max_height < $h) {
        $dw = (int)$w * ($max_height / $h);
        $dh = $max_height;
    }

    return generate_image($path . $imagename, $path . $imagename, $dw, $dh, 'resize', $filetype, $string, $jpeg_quality);
}