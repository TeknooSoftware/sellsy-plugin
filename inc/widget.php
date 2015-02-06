<?php
echo $beforeWidget;
if ($title) {
    echo $beforeTitle.$title.$afterTitle;
}
?>
<div>
    <?php
    echo $out;
    include_once WPI_PATH_INC.'/wp_sellsy-pub-page.php';
    ?>
</div>
<?php echo $after_widget;