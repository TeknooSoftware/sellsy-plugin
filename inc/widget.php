<?php
echo $beforeWidget;
if ($title) {
    echo $beforeTitle.$title.$afterTitle;
}
?>
<div>
    <?php
    echo $out;
    //Execute sellsy shortcode
    do_shortcode('[wpsellsy]');
    ?>
</div>
<?php echo $afterWidget;