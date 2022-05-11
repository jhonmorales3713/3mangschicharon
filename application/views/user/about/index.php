<style>
    .content{
        padding-top: 98px !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
        padding-bottom: 0 !important;
    }
</style>
<!-- <img src="<?= base_url('assets/img/about.png'); ?>" alt="" width="100%"> -->
<div class="row">
    
    <div class="col-12 h2 d-flex justify-content-center">About Us</div>
    <?php foreach($about_us as $info){?>

        <div class="col-12 h4 m-3"><?=$info['title']?></div>
        <div class="col-12 mr-3 ml-3"><?=$info['content']?></div>
        <br>
    <?php } ?>
</div>

