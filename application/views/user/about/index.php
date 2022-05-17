<style>
    .content{
        padding-top: 98px !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
        padding-bottom: 0 !important;
    }
    body{
        background-image: url(<?=base_url('assets/img/about_us.png')?>) !important; background-size: cover;
    }
</style>
<div class="row  d-flex justify-content-center ">
    
    <div class="col-8 bg-white   ">
        <div class="row">
            <div class="col-12 h2 font-weight-bold  d-flex justify-content-center">
            About Us
            </div>
            <?php foreach($about_us as $info){?>

                <div class="col-12 bg-white text-wrap d-flex justify-content-center h4 mt-4"><?=$info['title']?></div>
                <div class="col-12 bg-white  text-wrap d-flex justify-content-center mt-2"><?=$info['content']?></div>
                <br>
            <?php } ?>
        </div>
    </div>
</div>

