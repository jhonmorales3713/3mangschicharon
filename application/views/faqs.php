<link rel="stylesheet" href="<?=base_url('assets/css/libs/faqs.css')?>">  

<div class="row">
    <div class="container">
        <center>
        <h1>FAQs</h1>
        
        <hr>
        <div class="col-lg-12">
            <ul class="list-unstyled">
                <?php foreach($faqs as $faq){ ?>
                    <br>
                    <li>
                        <div class="col-12 question">
                            <strong><?= $faq['question']; ?></strong>
                        </div>
                        <div class="col-12 answer">
                            <span><?= $faq['answer']; ?></span>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        </div>
        </center>
    </div>
</div>