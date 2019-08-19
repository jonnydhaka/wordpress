<div class="wrap">
    <h1>Generate Demo Image</h1>
    <div class="progress sio-progress" style="height: 40px; display:none; " >
        <div class="progress-bar progress-bar-striped progress-bar-animated sio-demo-image-progressbar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"><span></span></div>
    </div>
    <div class="clearfix"> </div>
    <div class="mt-3">
        <p>
            <button class="btn btn-secondary btn-lg sio-demo-image-genaretor" data-length="<?php echo $sio_count ?>">
                    Generate Demo Image
            </button>

<?php
$display = '';
if (!is_dir($this->sio_rootdir . $this->sio_contentdir_target . $this->sio_contentdir_name)) {
    $display = 'display:none;';
}
?>
            <button class="btn btn-secondary btn-lg sio-demo-image-downloader" style="<?php echo $display; ?>">
                    Download Demo Image
            </button>
            <form  action="" method="post" style="<?php echo $display; ?>">

                <button class="btn btn-secondary btn-lg" type="submit" name="sio-xml-downloader" style="<?php echo $display; ?>">
                        Download XML
                </button>
            </form>
        </p>
    </div><!-- "mt-3 END  -->
    <div class="mt-3 sio-result-div">
    </div><!-- result-div END  -->
</div><!-- wrap END  -->
