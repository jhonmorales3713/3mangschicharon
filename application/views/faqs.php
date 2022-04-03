<link rel="stylesheet" href="<?=base_url('assets/css/libs/faqs.css')?>">  


<style>
    main {
        padding: 5vh 0 50px 0;
        background-image: url(<?=base_url("assets/img/coming-soon/coming-soon-web.jpg")?>) !important;
        background-size: cover;
        background-position: bottom;
        background-repeat: no-repeat;
        background-image: url(<?=base_url("assets/img/coming-soon/coming-soon-web")?>);
        background-attachment: fixed;
    }

</style>
<main class="merchant">
    <section class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-7">
                <h1 class="text-center orange font-weight-bold mb-5">FAQs</h1>
                <div id="accordion">
                    <div class="card">
                        <?php $i=0; foreach($faqs as $faq){ ?>
                            <div class="card-header" id="heading<?=$i?>" href="#" style="text-decoration:none;color:<?=cs_clients_info()->primary_color?>" data-toggle="collapse" data-target="#collapse<?=$i?>" aria-expanded="false" aria-controls="collapse<?=$i?>">
                                <h5 class="mb-0">
                                    <a>
                                        <?= $faq['title']; ?>
                                     </a>
                                </h5>
                            </div>
                            <div id="collapse<?=$i?>" class="collapse" aria-labelledby="heading<?=$i?>" data-parent="#accordion">
                                <div class="card-body">
                                    <?= $faq['content']; ?>
                                </div>
                            </div>
                        <?php $i++;} ?>
                    </div>
                </div>
            </div>
        </div>
    </section>     
</main>
  <style>
      .slick-track {
          display: flex;
          align-items: center;
      }
      .orange_link{
        color: #F6841F !important;
      }
  </style>

<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/merchant/merchant.css');?>">


