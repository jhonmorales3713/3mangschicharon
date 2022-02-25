// Use the following code to trigger the toast
// showCpToast("error", "Something went wrong", "The file your looking for does not exist.");

const baseUrl = $("body").data("base_url");

let toastStatus = false;

const showCpToast = (type="success", title="Success!", subtitle="Action is a success.", note='') => {
    let icon;
    switch(type) {
        case "success":
            icon = icons.success;
            break;
        case "info":
            icon = icons.info;
            break;
        case "warning":
            icon = icons.warning
            break;
        case "error":
            icon = icons.error
            break;
    }
    
    const cpToast = `
        <div class="cp-toast merchant-registration-modal text-left" id="submissionSuccess" style='z-index:99999 !important'>
            <div class="cp-toast-overlay cp-toast--overlay"></div>
            <div class="cp-toast-dialog">
                <div class="cp-toast-content">
                    <div class="cp-toast-header">
                        <div class="cp-toast-icon" style="">
                            ${icon}
                        </div>
                    </div>
                    <div class="cp-toast-body">
                        <h5 class=cp-toast-body-title>${title}</h5>
                        <div>${subtitle}</div>
                        <div class="mt-4"><b>${note}</b></div>
                    </div>
                    <!--<div class="cp-toast-footer text-center">
                        <div class="btn portal-primary-btn cp-toast--close cp-toast-button-primary">
                            Close
                        </div>
                    </div>-->
                </div>
            </div>
        </div>
    `
    
    $("body").toggleClass("cp-toast-body--hidden");
    $("body").append(cpToast);
    
    setTimeout(function(){
        $(".cp-toast").addClass("cp-toast--show");
        status = true;
    }, 1);

    setTimeout(function() {
        removeCpToast()
        status = false;
    }, 2000)
}

const removeCpToast = () => {
    $("body").removeClass("cp-toast-body--hidden");
    $(".cp-toast").removeClass("cp-toast--show");
    status = false;
    setTimeout(function(){
        $("#submissionSuccess").remove();
    }, 500);
}

$("body").on("click", ".cp-toast--close", function(){
    removeCpToast()
})

const icons = {
    success: `<?xml version="1.0" encoding="utf-8"?>
                <!-- Generator: Adobe Illustrator 24.1.2, SVG Export Plug-In . SVG Version: 6.00 Build 0)  -->
                <svg version="1.1" id="Layer_5" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                    viewBox="0 0 1006.8 894.6" style="enable-background:new 0 0 1006.8 894.6;" xml:space="preserve">
                <style type="text/css">
                    .st0{opacity:0.1;fill:var(--primary-color);}
                    .st1{fill:#CACACA;}
                    .st2{fill:none;stroke:var(--primary-color);stroke-width:5;stroke-miterlimit:10;}
                    .st3{fill:var(--primary-color);}
                    .st4{opacity:0.19;fill:var(--primary-color);}
                    .st5{opacity:0.31;fill:var(--primary-color);}
                    .st6{opacity:0.21;fill:var(--primary-color);}
                    .st7{opacity:0.7;fill:#6DB5EB;enable-background:new    ;}
                    .st8{fill:#D3E9F9;}
                    .st9{opacity:0.6;fill:#6DB5EB;enable-background:new    ;}
                    .st10{opacity:0.6;fill:var(--primary-color);enable-background:new    ;}
                    .st11{fill:#6DB5EB;}
                    .st12{fill:#6C63FF;}
                    .st13{opacity:0.2;enable-background:new    ;}
                    .st14{fill:#3F3D56;}
                    .st15{fill:#F0F0F0;}
                    .st16{fill:#FFFFFF;}
                    .st17{fill:#FF6584;}
                    .st18{fill:#CBCBCB;}
                    .st19{fill:#FFB6B6;}
                    .st20{fill:#2F2E41;}
                </style>
                <circle class="st0" cx="476.6" cy="486.2" r="394.5"/>
                <path class="st1" d="M691.2,817.1c-61.8,40.1-135.5,63.5-214.7,63.5c-77.9,0-150.5-22.6-211.7-61.6C107.2,825.8,0,839.1,0,854.4
                    c0,22.2,225.4,40.2,503.4,40.2s503.4-18,503.4-40.2C1006.8,837.5,876.2,823,691.2,817.1z"/>
                <circle class="st2" cx="544.4" cy="476.2" r="394.5"/>
                <path class="st3" d="M317.8,494.4L317.8,494.4c10.3-19,34-25.9,53-15.6l150.7,82.1c18.9,10.3,25.9,34,15.6,53l0,0
                    c-10.3,18.9-34,25.9-53,15.6l-150.7-82.1C314.5,537.1,307.5,513.4,317.8,494.4z"/>
                <path class="st3" d="M746.4,279.2L746.4,279.2c17.2,13.1,20.4,37.6,7.4,54.8l-217,284.2c-13.1,17.1-37.6,20.4-54.7,7.3l0,0
                    c-17.1-13.1-20.4-37.6-7.3-54.7l217-284.2C704.7,269.4,729.2,266.1,746.4,279.2z"/>
                <path class="st4" d="M926.9,815.1c12.1,0.1,24.1,0.1,36,0l-36-58.4h-0.4c-11.9,19.3-23.7,38.6-35.6,57.9
                    C902.7,814.8,914.7,815,926.9,815.1z"/>
                <path class="st3" d="M112,64.7c10.1,0.1,20.1,0.1,30,0L112,11h-0.3L82.1,64.2C92,64.4,101.9,64.6,112,64.7z"/>
                <path class="st5" d="M159.3,107.4c22.8,0.2,45.5,0.2,67.9,0L159.3,0h-0.8L91.4,106.5C113.9,106.9,136.5,107.2,159.3,107.4z"/>
                <circle class="st3" cx="914.9" cy="178.5" r="24"/>
                <circle class="st6" cx="938.9" cy="202.6" r="24"/>
                <ellipse transform="matrix(2.172375e-02 -0.9998 0.9998 2.172375e-02 -681.3541 827.4199)" class="st3" cx="82.1" cy="761.9" rx="24" ry="24"/>
                </svg>
                `,
    info: `<svg version="1.1" id="Layer_5" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                viewBox="0 0 1006.8 894.6" style="enable-background:new 0 0 1006.8 894.6;" xml:space="preserve">
            <style type="text/css">
            .st0{fill:#CACACA;}
            .st1{opacity:0.1;fill:var(--primary-color);}
            .st2{fill:none;stroke:var(--primary-color);stroke-width:5;stroke-miterlimit:10;}
            .st3{opacity:0.19;fill:var(--primary-color);}
            .st4{fill:var(--primary-color);}
            .st5{opacity:0.31;fill:var(--primary-color);}
            .st6{opacity:0.21;fill:var(--primary-color);}
            </style>
            <path class="st0" d="M691.2,817.1c-61.8,40.1-135.5,63.5-214.7,63.5c-77.9,0-150.5-22.6-211.7-61.6C107.2,825.8,0,839.1,0,854.4
            c0,22.2,225.4,40.2,503.4,40.2s503.4-18,503.4-40.2C1006.8,837.5,876.2,823,691.2,817.1z"/>
            <circle class="st1" cx="476.6" cy="486.1" r="394.5"/>
            <circle class="st2" cx="544.4" cy="476.2" r="394.5"/>
            <path class="st3" d="M926.9,815.1c12.1,0.1,24.1,0.1,36,0l-36-58.4h-0.4c-11.9,19.3-23.8,38.6-35.6,57.9
            C902.8,814.8,914.8,815,926.9,815.1z"/>
            <path class="st4" d="M112,64.7c10.1,0.1,20.1,0.1,30,0L112,11h-0.3L82.1,64.2C92,64.4,101.9,64.6,112,64.7z"/>
            <path class="st5" d="M159.4,107.3c22.9,0.2,45.5,0.2,67.9,0L159.4,0h-0.8L91.5,106.5C113.9,106.9,136.5,107.2,159.4,107.3z"/>
            <circle class="st4" cx="914.9" cy="178.5" r="24"/>
            <circle class="st6" cx="938.9" cy="202.6" r="24"/>
            <ellipse transform="matrix(2.172375e-02 -0.9998 0.9998 2.172375e-02 -681.3541 827.4199)" class="st4" cx="82.1" cy="761.9" rx="24" ry="24"/>
            <path class="st4" d="M380.3,364.1c0-22.1,6.6-44.4,19.9-67c13.3-22.6,32.7-41.4,58.2-56.3c25.5-14.9,55.2-22.3,89.2-22.3
            c31.6,0,59.5,6.2,83.7,18.6c24.2,12.4,42.9,29.3,56.1,50.7c13,20.9,19.9,45,19.8,69.7c0,19.7-3.8,37-11.3,51.9
            c-7,14.1-16,27.1-26.8,38.5c-10.3,10.8-28.9,29-55.7,54.5c-6.3,6-12.2,12.3-17.8,19c-3.9,4.6-7.3,9.6-10,15
            c-2.1,4.4-3.8,8.9-5.1,13.6c-1.2,4.5-3,12.5-5.4,23.9c-4.2,24.2-17.1,36.2-38.9,36.2c-10.8,0.2-21.2-4.1-28.6-11.8
            c-7.7-7.9-11.6-19.6-11.6-35.2c0-19.5,2.8-36.4,8.5-50.7c5.3-13.7,13-26.4,22.6-37.6c9.4-10.8,22-23.6,37.9-38.5
            c13.9-13,24-22.8,30.2-29.4c6.2-6.6,11.5-14.1,15.7-22.2c4.3-8.2,6.4-17.3,6.4-26.5c0-18.6-6.5-34.2-19.5-47S568.1,292,547.7,292
            c-24,0-41.7,6.4-53,19.3c-11.3,12.9-20.9,31.9-28.8,57c-7.4,26.3-21.5,39.4-42.2,39.4c-12.2,0-22.5-4.6-30.9-13.8
            C384.5,384.7,380.3,374.8,380.3,364.1z M539.8,745.9c-13,0.1-25.4-4.8-34.8-13.8c-9.9-9.2-14.9-22-14.9-38.5
            c0-14.6,4.8-26.9,14.4-36.9c9.1-9.8,21.9-15.2,35.3-15c13.2-0.2,25.8,5.2,34.7,15c9.4,10,14.1,22.3,14.1,36.9
            c0,16.3-4.9,29-14.7,38.3C564,741.2,552.7,745.9,539.8,745.9z"/>
            </svg>`,
    warning: `<svg version="1.1" id="Layer_5" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                    viewBox="0 0 1006.8 894.6" style="enable-background:new 0 0 1006.8 894.6;" xml:space="preserve">
                <style type="text/css">
                .st0{fill:#CACACA;}
                .st1{opacity:0.1;fill:var(--primary-color);}
                .st2{fill:none;stroke:var(--primary-color);stroke-width:5;stroke-miterlimit:10;}
                .st3{opacity:0.19;fill:var(--primary-color);}
                .st4{fill:var(--primary-color);}
                .st5{opacity:0.31;fill:var(--primary-color);}
                .st6{opacity:0.21;fill:var(--primary-color);}
                </style>
                <path class="st0" d="M691.2,817.9c-61.8,40.1-135.5,63.5-214.7,63.5c-77.9,0-150.5-22.6-211.7-61.6C107.2,826.6,0,839.9,0,855.2
                c0,22.2,225.4,40.2,503.4,40.2s503.4-18,503.4-40.2C1006.8,838.3,876.2,823.8,691.2,817.9z"/>
                <circle class="st1" cx="476.6" cy="486.1" r="394.5"/>
                <circle class="st2" cx="544.4" cy="476.2" r="394.5"/>
                <path class="st3" d="M926.9,815.1c12.1,0.1,24.1,0.1,36,0l-36-58.4h-0.4c-11.9,19.3-23.7,38.6-35.6,57.9
                C902.7,814.8,914.7,815,926.9,815.1z"/>
                <path class="st4" d="M112,64.7c10.1,0.1,20.1,0.1,30,0L112,11h-0.3L82.1,64.2C92,64.4,101.9,64.6,112,64.7z"/>
                <path class="st5" d="M159.3,107.4c22.8,0.2,45.5,0.2,67.9,0L159.3,0h-0.8L91.4,106.5C113.9,106.9,136.5,107.2,159.3,107.4z"/>
                <circle class="st4" cx="914.9" cy="178.5" r="24"/>
                <circle class="st6" cx="938.9" cy="202.6" r="24"/>
                <ellipse transform="matrix(2.172375e-02 -0.9998 0.9998 2.172375e-02 -681.3541 827.4199)" class="st4" cx="82.1" cy="761.9" rx="24" ry="24"/>
                <path class="st4" d="M559.1,210.5L559.1,210.5c21.6,0,39.1,17.5,39.1,39.1v357.5c0,21.6-17.5,39.1-39.1,39.1h0
                c-21.6,0-39.1-17.5-39.1-39.1V249.6C520.1,228,537.6,210.5,559.1,210.5z"/>
                <path class="st4" d="M559.1,683.5L559.1,683.5c21.6,0,39.1,17.5,39.1,39.1v0c0,21.6-17.5,39.1-39.1,39.1h0
                c-21.6,0-39.1-17.5-39.1-39.1v0C520.1,701,537.6,683.5,559.1,683.5z"/>
                </svg>`,
    error: `<svg version="1.1" id="Layer_5" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                viewBox="0 0 1006.8 894.6" style="enable-background:new 0 0 1006.8 894.6;" xml:space="preserve">
            <style type="text/css">
            .st0{fill:#CACACA;}
            .st1{opacity:0.1;fill:var(--primary-color);enable-background:new    ;}
            .st2{fill:none;stroke:var(--primary-color);stroke-width:5;stroke-miterlimit:10;}
            .st3{opacity:0.19;fill:var(--primary-color);enable-background:new    ;}
            .st4{fill:var(--primary-color);}
            .st5{opacity:0.31;fill:var(--primary-color);enable-background:new    ;}
            .st6{opacity:0.21;fill:var(--primary-color);enable-background:new    ;}
            </style>
            <path class="st0" d="M690.7,817.2c-61.8,40.1-135.5,63.5-214.7,63.5c-77.9,0-150.5-22.6-211.7-61.6C106.7,825.9-0.5,839.2-0.5,854.5
            c0,22.2,225.4,40.2,503.4,40.2s503.4-18,503.4-40.2C1006.3,837.6,875.7,823.1,690.7,817.2z"/>
            <circle class="st1" cx="476.6" cy="486.1" r="394.5"/>
            <circle class="st2" cx="544.4" cy="476.2" r="394.5"/>
            <path class="st3" d="M926.9,815.1c12.1,0.1,24.1,0.1,36,0l-36-58.4h-0.4l-35.6,57.9C902.7,814.8,914.8,815,926.9,815.1z"/>
            <path class="st4" d="M112,64.7c10.1,0.1,20.1,0.1,30,0L112,11h-0.3L82.1,64.2C92,64.4,101.9,64.6,112,64.7z"/>
            <path class="st5" d="M159.3,107.4c22.9,0.1,45.5,0.1,67.9,0L159.3,0h-0.8L91.5,106.5C113.9,106.9,136.5,107.2,159.3,107.4z"/>
            <circle class="st4" cx="914.9" cy="178.5" r="24"/>
            <circle class="st6" cx="938.9" cy="202.6" r="24"/>
            <ellipse transform="matrix(2.172375e-02 -0.9998 0.9998 2.172375e-02 -681.3541 827.4199)" class="st4" cx="82.1" cy="761.9" rx="24" ry="24"/>
            <path class="st4" d="M369.9,636.9L369.9,636.9c-22.5-12.1-26.9-35-9.7-50.8l286.5-263.7c17.2-15.8,49.8-18.9,72.3-6.8l0,0
            c22.5,12.1,26.9,35,9.7,50.8L442.2,630.1C425,645.9,392.4,649,369.9,636.9z"/>
            <path class="st4" d="M719,636.9L719,636.9c22.5-12.1,26.9-35,9.7-50.8L442.2,322.4c-17.2-15.8-49.8-18.9-72.3-6.8l0,0
            c-22.5,12.1-26.9,35-9.7,50.8l286.5,263.7C663.9,645.9,696.4,649,719,636.9z"/>
            </svg>`
}