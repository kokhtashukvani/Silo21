<?php

/*
Plugin Name: Link Silo Pro
Plugin URI:
Description: Link Silo Pro
Author: Kokhta Shukvani
Version: 0.1
Author URI:
*/
ini_set('display_errors', 0); ini_set('display_startup_errors', 0); error_reporting(E_ERROR);

function link_silo_create_db() {

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'linksilo_files';
    $table_name2 = $wpdb->prefix . 'linksilo_links';

    $sql = "	  	CREATE TABLE `$table_name` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `filename` varchar(300) NOT NULL ,
 `fileurl` varchar(1000) NOT NULL,
 `upload_date` varchar(100) NOT NULL DEFAULT current_timestamp(),
 `status` int(11) NOT NULL DEFAULT 0,
 PRIMARY KEY (`id`)
) $charset_collate;
";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
function link_silo_create_db_2() {

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'linksilo_files';
    $table_name2 = $wpdb->prefix . 'linksilo_links';

    $sql = "	 CREATE TABLE `$table_name2` (
 `linkid` int(11) NOT NULL AUTO_INCREMENT,
 `fileid` int(11) DEFAULT NULL,
 `post_id` int(11) DEFAULT NULL,
 `forward_posts_id` varchar(300) NOT NULL,
 `backward_post_id` varchar(300) NOT NULL,
 `created_date` varchar(100) NOT NULL DEFAULT current_timestamp(),
 `status` int(11) NOT NULL DEFAULT 0,
 PRIMARY KEY (`linkid`)
	                      ) $charset_collate
;
";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
register_activation_hook( __FILE__, 'link_silo_create_db' );
register_activation_hook( __FILE__, 'link_silo_create_db_2' );
function my_content_filter($content){
    //this is where we will implement our filter

  $Pmeta =  get_post_meta(get_the_ID(),'linkedTO',true);
    $content .= "<div id='RelatedPosts'><p id='RelatedPostsBefore'>Related Post: Followed by</p>";

                    $metaVersion= explode('**',$Pmeta);
                    $metaVersion= $metaVersion[count($metaVersion) -2];
                    $meta_explode= explode('|',$metaVersion);
                    foreach ($meta_explode as $meta){

                    $meta_Data= explode( ';',$meta);

                    if (!str_contains(strtolower( trim($content)),strtolower( trim($meta_Data[2]))) ){

                    $content .= "<p><a class='RelatedLink' href='" . $meta_Data[1] . "'>".$meta_Data[2]."</a></p>";
                    }
                    }
    $content .= "</div>";

    return $content;
}
add_filter( 'the_content', 'my_content_filter' );

add_action('admin_menu', 'LinkSilo_plugin_setup_menu');
add_action( 'wp_head', 'SiloAdminCSS' );
function SiloAdminCSS(){
    ini_set('display_errors', 0); ini_set('display_startup_errors', 0); error_reporting(E_ERROR);
    ?>
    <style>.wp-menu-image *  {transition: 600ms;transition-timing-function: ease-in-out}</style>
    <?php
}
function LinkSilo_plugin_setup_menu(){

    /** @noinspection CssInvalidPropertyValue */
    add_menu_page( 'LinkSilo Plugin Page', '<div class="wp-menu-name" style="font-weight: 600;color: darkslategrey;box-shadow: 0 0 45px -16px inset #0083ffb3;text-shadow: 0 0 0px #114576;color: white;letter-spacing: 0.6;text-decoration-line: underline;text-transform: capitalize;text-decoration-thickness: 1px;width: 100%;position: absolute;left: 0;top: 0;width: 118px;top: 2px;left: -5px;text-decoration-color: lightslategray;transition:300ms">LinkSilo Plugin</div>    <style>.wp-menu-image *  {transition: 600ms;transition-timing-function: ease-in-out;margin-top: 3px;}</style>', 'manage_options', 'LinkSilo-plugin', 'LinkSilo_init', '/wp-content/plugins/Link_Silo_Pro/assets/backlinkico.png', 6 );
    add_submenu_page('LinkSilo-plugin', 'Live Editor', 'Live Editor', 'manage_options', 'live_editor', 'LinkSilo_Live_Editor');
    add_submenu_page('LinkSilo-plugin', 'CSV History', 'CSV History', 'manage_options', 'csv_history', 'LinkSilo_CSV_History');
}

function LinkSilo_CSV_History()
{
}
function LinkSilo_init(){
ini_set('display_errors', 0); ini_set('display_startup_errors', 0); error_reporting(E_ERROR);

echo "<div id='mainCont'>";
    $header_img= plugin_dir_url( __FILE__ ).'assets/Header.jpg';
    $BG_IMG= plugin_dir_url( __FILE__ ).'assets/BG.jpg';
    $OverWriteIco= plugin_dir_url( __FILE__ ).'assets/saveIco.gif';
    ini_set('memory_limit', '512M');

    if ($_GET['action']=="undo"){
        $OldOption = get_option('PostIds_LinkSilo');
       // echo $OldOption . '<br>';
       $lastData= explode(";",$OldOption);
        $newOption="";
        for ($b=0;$b <= count($lastData) - 3;$b++){
            $newOption .= $lastData[$b] . ";";
        }

       // echo '<br>'. $newOption . '<br>';
       $lastData=$lastData[count($lastData)-2];


       $LastPosts=explode(',',$lastData);
      foreach ($LastPosts as $PID){
       $last_Post_Data = get_post_meta($PID,'linkedTO',true);
         // echo  '<br>'.  $last_Post_Data . '<br>';
         // echo  '<br>' . get_post_meta($PID,'linkedTO' ,true). '<br>';

          $last_Post_Data= explode('**',$last_Post_Data);
        $newPostData="";
          for ($i = 0;$i++; $i <= count($last_Post_Data) - 3) {
                        $newPostData .= $last_Post_Data[$i] . "**";
          }
          update_post_meta($PID,'linkedTO',$newPostData);

          // echo  '<br>' . get_post_meta($PID,'linkedTO' ,true). '<br>';

      }
//      echo  $PID . ' <=PID<br>' .var_dump( get_post_meta($PID,'linkedTO' ,true)). '<br>';
        ?>

        <div style="position: relative;display: block;width: 100%;height: auto;text-align: center;line-height: 4;padding-top: 40px;">
            <p style="font-size: 30px;text-shadow: 0 0 2px;"><span style="color: red;text-shadow: 0 0 4px red;">Undo</span> Completed Successfuly</p>
            <a href="/wp-admin/admin.php?page=LinkSilo-plugin" style="font-size: 20px;color: red;font-weight: bold;text-transform: inherit;text-decoration-line: none;background-color: #7b0000;padding: 10px;border-radius: 10px;box-shadow: 0 0 5px 3px #c30707,0 0 5px 7px darkslateblue , 0 0 25px 1px inset #a32c00, 0 0 4px 3px inset #060505;text-shadow: 0 0 5px #000;padding-left: 20px;padding-right: 20px;">GO Back</a>
        </div>

        <?php
       exit();


    }
?>

    <?php


    $FDFD=[new Silo()];
    $SSS=array();
    $Silos=  [];
    $Silo_Post=[];
    $SSDD=[];
//    echo "<h1>Link Silo</h1>";

    ?>
        <div style="width: 100%;height: auto;box-shadow: 0 0 20px -11px gold;">
            <img style="width: 100%;height: auto;border-bottom: 1px solid #ffdf3126;padding-bottom: 2px;" src="<?=$header_img?>">
        </div>
<div class="container-fluid" style="width: 70%;margin: auto;padding-top: 3rem;">
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="file" accept=".csv">
        <input type="submit" name="upload" style="padding: 20px;background-color: #87878724;box-shadow: 0 0 45px 15px #00000082 inset, 0 0 4px 0px gold;border-radius: 10px;border: 1px solid #d3d3d333;color: white;margin-left: 10px;">    </form>
</div>
    <div >
        <a id="UndoBtn" href="?page=LinkSilo-plugin&amp;action=undo" style="padding: 20px;background-color: #87878724;box-shadow: 0 0 45px 15px #00000082 inset, 0 0 4px 0px gold;border-radius: 10px;border: 1px solid #d3d3d333;color: white;margin-left: 10px;position: fixed;top: 50px;right: 50px;">Undo</a>
        <fieldset class="checkbox-group" style="display: none;">
            <div class="checkbox">
                <label class="checkbox-wrapper">
                    <input type="checkbox" class="checkbox-input" />
                    <span class="checkbox-tile">
				<span class="checkbox-icon">
<img src="<?=$OverWriteIco?>"/>
                </span>
				<span class="checkbox-label">OverWrite</span>
			</span>
                </label>
            </div>

        </fieldset>
    </div>
    <style>

        @import url("https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap");
        *,
        *:after,
        *:before {
            box-sizing: border-box;
        }

        body {
            font-family: "Inter", sans-serif;
            line-height: 1.5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f8f9;
        }

        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            width: 90%;
            margin-left: auto;
            margin-right: auto;
            max-width: 600px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        .checkbox-group > * {
            margin: 0.5rem 0.5rem;
        }

        .checkbox-group-legend {
            font-size: 1.5rem;
            font-weight: 700;
            color: #9c9c9c;
            text-align: center;
            line-height: 1.125;
            margin-bottom: 1.25rem;
        }

        .checkbox-input {
            clip: rect(0 0 0 0);
            -webkit-clip-path: inset(100%);
            clip-path: inset(100%);
            height: 1px;
            overflow: hidden;
            position: absolute;
            white-space: nowrap;
            width: 1px;
        }
        .checkbox-input:checked + .checkbox-tile {
            border-color: #2260ff;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            color: #2260ff;
        }
        .checkbox-input:checked + .checkbox-tile:before {
            transform: scale(1);
            opacity: 1;
            background-color: #2260ff;
            border-color: #2260ff;
        }
        .checkbox-input:checked + .checkbox-tile .checkbox-icon, .checkbox-input:checked + .checkbox-tile .checkbox-label {
            color: #2260ff;
        }
        .checkbox-input:focus + .checkbox-tile {
            border-color: #2260ff;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1), 0 0 0 4px #b5c9fc;
        }
        .checkbox-input:focus + .checkbox-tile:before {
            transform: scale(1);
            opacity: 1;
        }

        .checkbox-tile {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 7rem;
            min-height: 7rem;
            border-radius: 0.5rem;
            border: 2px solid #b5bfd9;
            background-color: #fff;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            transition: 0.15s ease;
            cursor: pointer;
            position: relative;
        }
        .checkbox-tile:before {
            content: "";
            position: absolute;
            display: block;
            width: 1.25rem;
            height: 1.25rem;
            border: 2px solid #b5bfd9;
            background-color: #fff;
            border-radius: 50%;
            top: 0.25rem;
            left: 0.25rem;
            opacity: 0;
            transform: scale(0);
            transition: 0.25s ease;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='192' height='192' fill='%23FFFFFF' viewBox='0 0 256 256'%3E%3Crect width='256' height='256' fill='none'%3E%3C/rect%3E%3Cpolyline points='216 72.005 104 184 48 128.005' fill='none' stroke='%23FFFFFF' stroke-linecap='round' stroke-linejoin='round' stroke-width='32'%3E%3C/polyline%3E%3C/svg%3E");
            background-size: 12px;
            background-repeat: no-repeat;
            background-position: 50% 50%;
        }
        .checkbox-tile:hover {
            border-color: #2260ff;
        }
        .checkbox-tile:hover:before {
            transform: scale(1);
            opacity: 1;
        }

        .checkbox-icon {
            transition: 0.375s ease;
            color: #494949;
        }
        .checkbox-icon svg {
            width: 3rem;
            height: 3rem;
        }

        .checkbox-label {
            color: #707070;
            transition: 0.375s ease;
            text-align: center;
        }










*{transition: 300ms!important;}
        .acardeon.close_acar {
            height: 80px;
            overflow: hidden;
        }
        .acardeon{transition: 500ms;  overflow: hidden;}
        .acardeon *{transition: 500ms}

        #mainCont {
            background-color: #191919;
            color: white;
            width: 110%;
            margin-left: 0;
            background-image: url("https://kokhta.com/wp-content/plugins/Link_Silo_Pro/assets/BG.jpg");
            background-size: 100% 100%;
            overflow: scroll;
            height: unset;
            text-align: center;
            padding: 10%;
            right: 0;
            left: 0;
            top: 0;
            width: auto;
            height: auto;
            padding: 0;
            display: flow-root;
            overflow: scroll;
            height: 100%;min-height: 767px;
            width: 100%;
        }
        #adminmenuback{
            left: 0;

        }
        #adminmenuwrap {
            border-right: 1px solid #ffd70042 !important;
            top: 0 !important;
        }
        #wpcontent {
            height: 100%;
            padding-left: 0px;
        }
        #wpadminbar {
            background-color: #191919;
            border-bottom: 1px solid #ffd7002b;
        }
        #adminmenuwrap {
            border-right: 1px solid #ffd70042 !important;
        }
        .wp-admin input[type="file"] {
            padding: 3px 0;
            cursor: pointer;
            padding: 20px;
            background-color: #87878724;
            box-shadow: 0 0 45px 15px #5151511f inset;
            border-radius: 10px;
            border: 1px solid #d3d3d333;
            padding: 20px;
            background-color: #87878724;
            box-shadow: 0 0 45px 15px #5151511f inset, 0 0 4px 0px gold;
            border-radius: 10px;
            border: 1px solid #d3d3d333;
        }
        .checkbox-tile {
            background-color: #ffd7003d;
        }
        .checkbox-icon img{
            mix-blend-mode: multiply;
            isolation: isolate;
            filter: brightness(1.3) grayscale(963%) contrast(1);
        }
        .checkbox-label {
            color: white !important;
            text-shadow: 0px 0 3px white;
            margin-bottom: -25px;
            width: 100%;
            border-top: 2px solid #ffffff57;
            box-shadow: 0 0 16px 1px inset #ffd70070, 0px -7px 8px -9px;
            border-top: 2px solid #ffd700a3;
        }
        .checkbox-input:checked + .checkbox-tile {
            border-color: #ffd7007a;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            color: #2260ff;
            box-shadow: 0 0 22px 0px #ffd70082 inset;
        }
        .checkbox-input:checked + .checkbox-tile::before {
            transform: scale(1);
            opacity: 1;
            background-color: #8bff0078;
            border-color: #ffd70082;
            box-shadow: 0 0 5px 5px inset #ff780075;
        }
    </style>
    <?php
    if(isset($_POST['upload']))
    {
    ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ERROR);

    $Script="";
        if( ! empty( $_FILES ) )
        {
            $file=$_FILES['file'];
            $attachment_id = upload_user_file( $file );
            $CsvUrl=     wp_get_attachment_url($attachment_id);
            $filename=explode('/',$CsvUrl);
            $filename=$filename[count($filename)-1];
    global $wpdb;
    $table_name = $wpdb->prefix . 'linksilo_files';
    $wpdb->insert($table_name, array('filename' => $filename, 'fileurl' => $CsvUrl));

//            $CsvUrl='http://127.0.0.1/wp-content/uploads/2023/03/coop-34.csv';

            $CSVfp = fopen($CsvUrl, "r");
            if ($CSVfp !== FALSE) {

                ?>

                <?php

                $i=0;
                while (! feof($CSVfp)) {
//                    if ($i > 0){$Script .= ",";}
                    $Script ="";

                    $data = fgetcsv($CSVfp, 1000, ",");

                    if (! empty($data)) {

//                        foreach ($data as $datum) {

//                            echo '<pre>';var_dump();echo "</pre>";

//                    $Silos[]=new Silo();
                        $S=new Silo();
                        $FDFD[]=$S;
                        $S->set_ReplacementText($data[0]);
                        $S->set_ReplacementURL($data[1]);
                        $S->set_SearchPhrase($data[2]);
//                            array_push($SSS,$S);
//                        $FDFD  [$ii]=$S;
                            $Silos[$i][0]= $data[2];
                            $Silos[$i][1]= $data[0];
                            $Silos[$i][2]= $data[1];

//$i++;

                        global $wpdb,$post;

                        $StrSearch=str_replace('"','',$data[2]);
                        global $wpdb,$post;
                        $myposts = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->posts WHERE  post_status ='publish' and post_type='post' and post_title LIKE '%s'", '%'. $wpdb->esc_like( $StrSearch ) .'%') );
//                        $myposts = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE post_status='publish' and post_type='post' and post_title LIKE  %s \"%" .    $data[2]   ."%\"" );
//var_dump($myposts);
//echo "SELECT * FROM $wpdb->posts WHERE post_status='publish' and post_type='post' and post_title LIKE \"%" .   $data[2]  ."%\"";
//                        $Silos[$i]['posts']=$myposts;
                        $Script .= "

  name: 'KeyWord:$data[2]',
  fixed: false,  
        children: [
           ";


                        $gg=0;$V=0;
                        foreach ($myposts as $item) {
                        $Silo_Post[$i][$gg]=$item->ID;
                            if ($gg > 0){$Script .= ",";}
                            $gg++;
                            echo "<h1 style='color:yellow;font-size: 10px;text-align: left;margin-left: 10rem;'>".$item->post_title."</h1>";
                            $post_id = $item->ID;

                        }

                        ?>

                    <?php }
                    ?>

                    <?php


                    $i++;

                }?>


                <div class="phppot-container">

                    <?php
                ?>

                </div>
                <style>
                    .Sectionh3{
                        font-size: 13px;
                        margin: 0;
                        text-align: center;
                        background-color: gainsboro;
                        padding: 10px;

                    }
                    .Sections {
                        height: 34px;
                        overflow: hidden;
                        background: red;
                        margin-top: 10px;
                        margin-bottom: 10px;
                        padding: 10px;
                        padding: 0;
                    }

                </style>
                <?php


                fclose($CSVfp);
            }

            for ($C = 0; $C <= count($Silos) - 1 ; $C++) {


                ?>
<div class="acardeon">
                <h3 onclick="jQuery(this).parent().toggleClass('close_acar');" class="TitleBarAcar" style="min-width:70%;color:white;background-color: #ffffff70;width: auto;display: inline;color: black;padding: 7px;box-shadow: 0 0 15px 1px inset #2d2d2d;border-radius: 10px;padding-left: 30px;padding-right: 30px;border: 1px solid #333;display: inline-flex;line-height: 1.8;text-shadow: 0px 0px 9px black;color: honeydew;"><img src="https://www.iconpacks.net/icons/2/free-arrow-down-icon-3101-thumb.png" style="max-height: 30px;margin-right: 30px;filter: blur(0pt);"><img src="https://www.iconpacks.net/icons/2/free-arrow-down-icon-3101-thumb.png" style="max-height: 30px;margin-right: 30px;filter: blur(1.5pt) brightness(100%) contrast(100%);margin-left: -60px;">
                    <?= $Silos[$C][0]?>>
                </h3>
                    <?php
                foreach ($Silo_Post[$C] as $SlavePID)
                {

                    foreach ($Silo_Post[$C] as $MasterPID)
                    {
                   $Title=    get_the_title($MasterPID);
                   $STitle=get_the_title($SlavePID);
                        echo "<p style=\"margin-left: 10%;text-align: left;max-width: 60%;margin-left: 30%;\" >==> $STitle </p>";

                   $post_Link=get_permalink($MasterPID);
                        $CurrentMeta= get_post_meta($SlavePID,'linkedTO',true);

                        update_post_meta($SlavePID, "linkedTO",$CurrentMeta . '|' .$MasterPID . ";" .$Silos[$C][2] . ";".$Silos[$C][1]);
                    }
                }
                if (!isset($Silos[$C - 1][0]))
                {

                    foreach ($Silo_Post[count($Silos) - 1] as $SlavePID)
                    {

                        foreach ($Silo_Post[$C] as $MasterPID)
                        {
                            $STitle=get_the_title($SlavePID);
                            $Title=    get_the_title($MasterPID);
                            echo "<p style=\"margin-left: 10%;text-align: left;max-width: 60%;margin-left: 30%;\" >==> $STitle </p>";

                            $post_Link=get_permalink($MasterPID);
                            $CurrentMeta= get_post_meta($SlavePID,'linkedTO',true);
                            update_post_meta($SlavePID, "linkedTO",$CurrentMeta . '|' .$MasterPID . ";" .$post_Link . ";".$Title);
                        }
                    }


                }
                else {

                        foreach ($Silo_Post[$C - 1] as $SlavePID)
                        {

                            foreach ($Silo_Post[$C] as $MasterPID)
                            {
                                $STitle=get_the_title($SlavePID);
                                $Title=    get_the_title($MasterPID);
                                echo "<p style=\"margin-left: 10%;text-align: left;max-width: 60%;margin-left: 30%;\" >==> $STitle </p>";


                                $post_Link=get_permalink($MasterPID);
                                $CurrentMeta= get_post_meta($SlavePID,'linkedTO',true);
                                update_post_meta($SlavePID, "linkedTO",$CurrentMeta . '|' .$MasterPID . ";" .$post_Link . ";".$Title);

                            }
                        }
                }


                if (!isset($Silos[$C + 1][0])){



                    foreach ($Silo_Post[0] as $SlavePID)
                    {

                        foreach ($Silo_Post[$C] as $MasterPID)
                        {
                            $STitle=get_the_title($SlavePID);
                            $Title=    get_the_title($MasterPID);
                            echo "<p style=\"margin-left: 10%;text-align: left;max-width: 60%;margin-left: 30%;\" >==> $STitle </p>";

                            $post_Link=get_permalink($MasterPID);
                            $CurrentMeta= get_post_meta($SlavePID,'linkedTO',true);
                            update_post_meta($SlavePID, "linkedTO",$CurrentMeta . '|' .$MasterPID . ";" .$post_Link . ";".$Title);


                        }
                    }

                }
                else {




                    foreach ($Silo_Post[$C + 1] as $SlavePID)
                    {

                        foreach ($Silo_Post[$C] as $MasterPID)
                        {
                            $STitle=get_the_title($SlavePID);
                            $Title=    get_the_title($MasterPID);
                            echo "<p style=\"margin-left: 10%;text-align: left;max-width: 60%;margin-left: 30%;\" >==> $STitle </p>";

                            $post_Link=get_permalink($MasterPID);
                            $CurrentMeta= get_post_meta($SlavePID,'linkedTO',true);
                            update_post_meta($SlavePID, "linkedTO",$CurrentMeta . '|' .$MasterPID . ";" .$post_Link . ";".$Title);


                        }
                    }


                      }



?>


                <?php
echo '</div>';
            }
            $pidList='';
         for ($C = 0; $C <= count($Silos) - 1 ; $C++) {

                foreach ($Silo_Post[$C] as $pid) {
                    $pidList .= $pid . ",";
                    $CurrentMeta= get_post_meta($pid,'linkedTO',true);
                    update_post_meta($pid, "linkedTO",$CurrentMeta . '**');
                }


            }
         ?>
            <h1 style="color: white">Operation Finished Successfuly</h1>
<?php
            $pidList .= ";";
            $OldOption = get_option('PostIds_LinkSilo');
            update_option('PostIds_LinkSilo',$OldOption . $pidList);


        }
    }
    ?>

    <?php

    ?>

    <?php

    echo "</div>";
}

Class SiloPro{


}
function upload_user_file( $file = array() ) {

    require_once( ABSPATH . 'wp-admin/includes/admin.php' );

    $file_return = wp_handle_upload( $file, array('test_form' => false ) );

    if( isset( $file_return['error'] ) || isset( $file_return['upload_error_handler'] ) ) {
        return false;
    } else {

        $filename = $file_return['file'];

        $attachment = array(
            'post_mime_type' => $file_return['type'],
            'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
            'post_content' => '',
            'post_status' => 'inherit',
            'guid' => $file_return['url']
        );

        $attachment_id = wp_insert_attachment( $attachment, $file_return['url'] );

        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );
        wp_update_attachment_metadata( $attachment_id, $attachment_data );

        if( 0 < intval( $attachment_id ) ) {
            return $attachment_id;
        }
    }

    return false;
}

class Silo {
    // Properties
    public $SearchPhrase;
    public $ReplacementText;
    public $ReplacementURL;

    // Methods
    function set_SearchPhrase($SearchPhrase) {
        $this->$SearchPhrase = $SearchPhrase;
    }
    function get_SearchPhrase() {
        return $this->SearchPhrase;
    }
    // Methods
    function set_ReplacementText($ReplacementText) {
        $this->$ReplacementText = $ReplacementText;
    }
    function get_ReplacementText() {
        return $this->ReplacementText;
    }
    // Methods
    function set_ReplacementURL($ReplacementURL) {
        $this->$ReplacementURL = $ReplacementURL;
    }
    function get_ReplacementURL() {
        return $this->ReplacementURL;
    }

}


// php function to convert csv to json format
function csvToJson($fname)
{
    // open csv file
    if (!($fp = fopen($fname, 'r'))) {
        die("Can't open file...");
    }

    //read csv headers
    $key = fgetcsv($fp, "1024", ",");
    // parse csv rows into array
    $json = array();
    $i=0;
    while ($row = fgetcsv($fp, "1024", ",")) {

        $json[$i] = array_combine($key, $row);
    }

    // release file handle
    fclose($fp);

    // encode array to json
    return json_encode($json);
}

function afunction( $post, $new_title ) {
    // if new_title isn't defined, return
    if ( empty ( $new_title ) ) {
        return;
    }

    // ensure title case of $new_title
    $new_title = mb_convert_case( $new_title, MB_CASE_TITLE, "UTF-8" );

    // if $new_title is defined, but it matches the current title, return
    if ( $post->post_title === $new_title ) {
        return;
    }
    $new_title = str_replace('"','',$new_title);
    $new_title = str_replace('“','',$new_title);
    // place the current post and $new_title into array
    $post_update = array(
        'ID'         => $post->ID,
        'post_title' => $new_title
    );

    wp_update_post( $post_update );
}
include_once ('live_editor.php');
include_once ('file_history.php');
?>
