<div class="container-fluid">
    <div class="row">
    <div class="col-12">
    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
            <img class="d-block w-100" src="<?= base_url('uploads/banners/b1.jpg') ?>" alt="">
            </div>
            <div class="carousel-item">
            <img class="d-block w-100" src="<?= base_url('uploads/banners/b2.jpg') ?>" alt="">
            </div>   
            <div class="carousel-item">
            <img class="d-block w-100" src="<?= base_url('uploads/banners/b3.jpg') ?>" alt="">
            </div>          
        </div>
        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="carousel-control-next-icon text-warning" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
        </div>    
    </div>
    </div>
</div>

<script src="<?= base_url('assets/js/libs/user/home/home.js'); ?>"></script>