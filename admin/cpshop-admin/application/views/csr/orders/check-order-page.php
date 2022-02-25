<style type="text/css">
@media only screen and (max-height: 460px) {
    .checkout__place-order {
        position: relative !important;
        bottom: 0px !important;
        z-index: 1 !important;
    }
}

/* Custom, iPhone Retina */
@media only screen and (min-width: 321px) {
}

/* Extra Small Devices, Phones */
@media only screen and (min-width: 480px) {
}

/* col-sm media query*/
@media only screen and (min-width: 576px) {
}

/* Small Devices, Tablets */
@media only screen and (min-width: 768px) {
    .search-page .product-container {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

@media only screen and (min-width: 768px) {
    .product__container {
        flex: 0 0 33.33%;
        max-width: 33.33%;
        padding-right: 0.6vw !important;
        padding-left: 0.6vw !important;
    }
}

/* Medium Devices, Desktops */
@media only screen and (min-width: 992px) {
    .search__button i {
        font-size: 20px;
    }

    .detail-card {
        padding-left: 20px;
        padding-right: 20px;
    }

    .bottom-nav {
        display: none;
    }

    .checkout__footer.checkout__footer--close {
        transform: translateY(60%);
    }

    .product__container {
        padding-right: 15px !important;
        padding-left: 15px !important;
    }
    .footer__header {
        margin-bottom: 24px;
    }

    .search-page .product-container {
        flex: 0 0 20%;
        max-width: 20%;
    }

    /* .product__container {
        flex: 0 0 25%;
        max-width: 25%;
        padding-right: 0.6vw !important;
        padding-left: 0.6vw !important;
    } */
}

/* Large Devices, Wide Screens */
@media only screen and (min-width: 1200px) {
    .container {
        max-width: 1360px;
    }
}

@media only screen and (min-width: 1360px) {
    .product__container {
        flex: 0 0 25%;
        max-width: 20%;
        padding-right: 0.6vw !important;
        padding-left: 0.6vw !important;
    }
}

/*==========  Non-Mobile First Method  ==========*/

/* Large Devices, Wide Screens */
@media only screen and (max-width: 1199px) {
    footer {
        padding-bottom: 1em;
    }
}

@media only screen and (max-width: 1600px) and (min-width: 992px) and (min-height: 700px) {
    body {
        font-size: 12px;
    }
}

/* Medium Devices, Desktops */
@media only screen and (max-width: 991px) {
    
    .single-product-summary .store {
        font-size: 9.5px !important;
    }
    
    .single-product-text {
        font-size: 20px;
    }

    .portal-table__totalprice {
        font-weight: bold;
        font-size: 20px;
    }

    .sidenav {
        display: none;
    }

    .mobile__dropdown--display .branch__dropdown__mobile,
    .mobile__dropdown--display .branch__dropdown__mobile--overlay {
        display: block;
    }

    .branch__dropdown__mobile {
        position: fixed;
        top: 5em;
        left: 1em;
        right: 1em;
        /* bottom: 0; */
        z-index: 10000000;
        display: block;
        display: none;
    }
    /* 
    .branch__dropdown__mobile-container {
    } */

    .branch__dropdown__mobile--overlay {
        display: none;
        content: "";
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.4);
        z-index: 1;
    }

    .sidenav__mobile {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        width: var(--sidemenu-wd);
        background-color: #fff;
        z-index: 101000;
        box-shadow: var(--box-shadow);
        display: none;
    }

    .sidenav__mobile__header {
        position: relative;
    }

    .sidenav__mobile__close-icon {
        position: absolute;
        top: 50%;
        right: 20px;
        transform: translateY(-50%);
        color: #fff;
        font-size: 1.5em;
    }

    .sidenav__mobile--overlay {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        background-color: rgba(0, 0, 0, 0.4);
        z-index: 1009;
        display: none;
    }

    .header__item__mobile.sidenav__mobile--display .sidenav__mobile,
    .header__item__mobile.sidenav__mobile--display .sidenav__mobile--overlay {
        display: block;
    }
    .sidenav__mobile__header {
        background-color: var(--primary-color);
    }

    .sidenav__mobile__header .dropdown__header {
        display: flex;
        flex-direction: row;
    }

    .sidenav__mobile__header .dropdown__name {
        border: none;
        font-size: 1.5em;
        color: #fff;
    }

    .sidenav__mobile__content {
        display: flex;
        flex-direction: column;
    }

    .sidenav__mobile__item {
        padding: 1.5em 2em;
    }

    .sidenav__mobile__item:hover {
        background-color: #eee;
    }

    .header__mobile-title {
        color: var(--black);
        font-weight: 700;
    }

    .portal-table__titles {
        display: none;
    }

    .portal-table {
        width: 100%;
    }

    .portal-table__column {
        /* margin-bottom: 0.5em; */
        padding-left: 10px;
        padding-right: 10px;
    }

    .inventory-item-page .portal-table__item {
        padding-left: 0;
        padding-right: 0;
    }

    .inventory-item-page .portal-table__column {
        padding-left: 15px;
        padding-right: 15px;
    }

    .portal-table__column:last-child {
        margin-bottom: 0;
    }

    .portal-table__item {
        padding: 1em;
    }
    /*  
    .quantity__minus {
        left: 5px;
    }

    .quantity__plus {
        right: 5px;
    } */

    /* ============================== column order ========================== */

    .portal-table__id {
        order: 0;
    }
    .portal-table__product {
        order: 1;
    }
    .portal-table__price {
        order: 2;
        color: #191919;
        font-weight: 700;
    }
    .portal-table__unit {
        order: 3;
    }

    .portal-table__unit .ui-selectmenu-button {
        order: 3;
    }

    .portal-table__unit .ui-selectmenu-button .ui-selectmenu-text {
        order: 3;
    }

    .portal-table__category {
        order: 3;
        margin-bottom: 1em;
    }
    .portal-table__quantity {
        order: 3;
        margin-bottom: 1em;
    }
    .portal-table__button {
        order: 3;
    }

    .portal-table__delete {
        order: 10;
    }

    /* checkout page */
    .checkout-page {
        padding-top: 3em;
        margin-bottom: 40px;
        overflow-y: auto;
    }

    .checkout-page .portal-table__item {
        border-bottom: 1px solid gainsboro;
        border-radius: 0;
    }

    .checkout-page .portal-table__price {
        margin-bottom: 1em;
    }

    .checkout-page .portal-table__unit {
        order: 4;
    }

    .checkout-page .portal-table__category {
        order: 5;
        margin-bottom: 1em;
    }
    .checkout-page .portal-table__quantity {
        order: 3;
        margin-bottom: 0;
    }

    .portal-table__srno {
        color: var(--black);
        font-weight: 700;
    }

    .portal-table__branch select {
        border: 1px solid var(--gray);
        padding: 0.5em;
        border-radius: 30px;
        /* background-color: transparent; */
    }

    .order-page .portal-table__srno {
        order: 1;
    }
    .order-page .portal-table__drno {
        order: 2;
    }
    .order-page .portal-table__branch {
        order: 4;
    }
    .order-page .portal-table__date {
        order: 0;
    }
    .order-page .portal-table__total {
        order: 1;
        color: var(--black);
        font-weight: 700;
    }
    .order-page .portal-table__status {
        order: 3;
    }

    .order-page .portal-table__button {
        order: 4;
    }

    .inventory-page .portal-table__product {
        margin-bottom: 1em;
    }

    .inventory-page .portal-table__unit,
    .inventory-page .portal-table__category,
    .inventory-page .portal-table__quantity {
        color: var(--black);
    }

    /* ================================== column order ======================= */

    .portal-table__quantity input,
    .ui-selectmenu-button {
        height: 40px !important;
    }

    .portal-table__button > .btn {
        width: 100%;
    }

    /* .checkout-page {
        padding-bottom: 8em;
    } */

    .checkout__footer-container {
        padding: 0;
        left: 0;
        right: 0;
        bottom: 60px;
    }

    .checkout__footer {
        padding-bottom: 1em;
    }

    .checkout__footer-container .container-fluid,
    .checkout__footer-container .container-fluid > .col-12 {
        padding: 0;
    }

    .checkout__place-order {
        /* position: fixed; */
        background-color: #fff;
        /* bottom: 60px;
        left: 0;
        right: 0; */
        padding: 1em;
        z-index: 1005;
        display: flex;
        align-items: flex-end;
        border-top: 1px solid var(--gray);
        border-bottom: 1px solid var(--gray);
    }

    .checkout__place-order__checkOrder {
        max-height: 8vh;
        position: fixed;
        background-color: #fff;
        bottom: 0px;
        left: 0;
        right: 0;
        padding: 0em 1em 1em 1em;
        z-index: 1005;
        display: flex;
        align-items: center;
        border-top: 1px solid var(--gray);
        border-bottom: 1px solid var(--gray);
    }

    .checkout__total {
        font-size: 16px;
        padding-top: 1em;
        padding-bottom: 1em;
    }
    /* .cart__button.total__item {
        font-size: 14px;
    } */

    /* .cart-page__button {
        padding-top: 1em;
        padding-bottom: 1em;
    } */

    .checkout__button {
        display: inline;
    }

    .checkout__total > .total__item {
        font-size: 16px;
        margin-bottom: 0;
        align-self: center;
    }

    .checkout__footer.checkout__footer--close .checkout__left-content,
    .checkout__footer.checkout__footer--close .checkout__dates {
        display: none;
    }

    .checkout__dates {
        margin-top: 1.5em;
    }

    .checkout__footer .footer__header {
        font-size: 1.3em;
    }

    .receiver__title {
        font-size: 1em;
    }

    .checkout__footer.checkout__footer--close .footer__header {
        /* margin-bottom: 0 !important; */
    }

    .checkout__order .portal-table__item {
        background-color: #fff;
    }

    .checkout__order .portal-table__totalprice {
        color: #ff4444;
    }

    .checkout-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: #fff;
        -webkit-box-shadow: 0px 3px 5px 0px rgba(0, 0, 0, 0.31);
        -moz-box-shadow: 0px 3px 5px 0px rgba(0, 0, 0, 0.31);
        box-shadow: 0px -1px 5px 0px rgba(0, 0, 0, 0.31);
        margin-bottom: 0;
        padding: 0;
    }

    .checkout-button {
        border-radius: 0;
    }
}
/* =========================================================================================

Portal Timeline

=========================================================================================== */

.portal-tracker {
    background-color: #fff;
    padding: 2em;
    margin-bottom: 2em;
    border-radius: 20px;
    box-shadow: var(--box-shadow);
}

.portal-timeline {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    position: relative;
    margin-bottom: 40px;
}

.portal-timeline__item {
    position: relative;
}

.portal-timeline__node {
    width: 25px;
    height: 25px;
    background-color: var(--gray);
    border-radius: 50%;
}

.portal-timeline__item.portal-timeline__item--active .portal-timeline__node {
    border: 5px solid var(--primary-color);
    background-color: #fff;
}

.portal-timeline__title {
    position: absolute;
    top: 40px;
    left: 50%;
    transform: translateX(-50%);
    white-space: nowrap;
}

.portal-timeline__item.portal-timeline__item--active .portal-timeline__title {
    display: block;
    color: var(--primary-color);
    font-weight: 600;
}

.portal-timelime__bg-line-container {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    transform: translateY(-50%);
    height: 3px;
    display: flex;
    flex-direction: row;
}

.portal-timeline__line {
    height: 100%;
    width: 50%;
    background-color: var(--gray);
}

.portal-timeline__line.portal-timeline__line--active {
    background-color: var(--primary-color);
}

/* portal timeline vertical */
 #order_status_parent_collapse {
    /*cursor: pointer;*/
}

#order_status_parent_collapse:hover {
    /*background-color: #f6f6f6;*/
} 

.portal-timeline-vertical {
    display: inline-block;
    position: relative;
    width: 100%;
    font-size: 12px;
}

.portal-timeline-vertical__item {
    position: relative;
    margin-bottom: 20px;
    display: flex;
    flex-wrap: nowrap;
    align-items: center;
}

.portal-timeline-vertical__node {
    width: 25px;
    height: 25px;
    background-color: var(--gray);
    border-radius: 50%;
}

.portal-timeline-vertical__node>.fa{
  color: #28a745;
  position: absolute;
  top: 6px;
  left: 7px;
  font-size: 12px;
}

 .portal-timeline-vertical>.portal-timeline-vertical__item:not(:first-child)>.portal-timeline-vertical__node:before {
    content: "";
  left: 3px;
  bottom: 33px;
  display: block;
  position: absolute;
  width: 19px;
  transform: rotate(90deg);
  padding: 2px 0;
  background-color: var(--gray);
}

.portal-timeline-vertical__item.portal-timeline-vertical__item--active .portal-timeline-vertical__node {
    border: 5px solid #28a745;
    background-color: #fff;
}

.portal-timeline-vertical__item.portal-timeline-vertical__item--active .portal-timeline-vertical__node:before {
    background-color: #28a745 !important;
}

.portal-timeline-vertical__title {
    white-space: nowrap;
    padding-left: 10px;
}

.portal-timeline-vertical__item.portal-timeline-vertical__item--active .portal-timeline-vertical__title, .portal-timeline-vertical__timestamp {
    display: block;
    color: #28a745;
    font-weight: 600;
}

.portal-timelime__bg-line-container {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    transform: translateY(-50%);
    height: 3px;
    display: flex;
    flex-direction: row;
}

.portal-timeline__line {
    height: 100%;
    width: 50%;
    background-color: var(--gray);
}

.portal-timeline__line.portal-timeline__line--active {
    background-color: var(--primary-color);
}

/* End portal timeline vertical */

.portal-tracker__detail-item {
    background-color: #fff;
    padding: 1em;
    border: 2px solid var(--primary-color);
    border-radius: 10px;
}

.portal-tracker__arrow-container {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    position: relative;
}

.portal-tracker__arrow {
    height: 15px;
    width: 15px;
    transform: rotate(45deg) translate(50%, 50%);
    background-color: #fff;
    border-top: 2px solid var(--primary-color);
    border-left: 2px solid var(--primary-color);
    bottom: 3px;
    position: relative;
    display: none;
}

.portal-tracker__arrow.portal-tracker__arrow--display {
    display: block;
}

.portal-tracker__detail-description {
    color: var(--black);
    margin-bottom: 1em;
}

.branch__menu {
    padding: 1em 2em 1em 1em;
}

.branch__item {
    display: flex;
    flex-direction: row;
    align-items: center;
    position: relative;
    margin-bottom: 1em;
}

.branch__item:last-child {
    margin-bottom: 0;
}

.branch__logo {
    height: 1em;
    margin-right: 0.7em;
    display: flex;
    align-items: center;
}

.branch__logo > img {
    height: 100%;
}

.profile__menu {
    display: flex;
    flex-direction: row;
    padding: 1em 2em 1em 1em;
}

.profile__image {
    height: var(--image-ht);
    /* padding-bottom: 100%; */
    margin-right: 0.7em;
    overflow: hidden;
}

.profile__image > img {
    border-radius: 50%;
    height: 100%;
    /* position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%); */
}

/* .cart__menu {
    padding: 1em;
} */

.cart__notif {
    position: relative;
}

.cart__notif i,
.mobile-header-cart i {
    position: relative;
}

.cart__notif__number {
    position: absolute;
    top: 50%;
    right: -10px;
    transform: translateY(-100%);
    background-color: var(--success);
    height: 1.2em;
    width: 1.2em;
    border-radius: 50%;
    text-align: center;
    z-index: 10000;
}

.cart__notif__number > span {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #fff;
    font-size: 11px;
}

.cart__menu i {
    color: var(--primary-color);
    /* font-size: 20px; */
}

.notif__menu {
    padding: 1em;
}

.notif__menu i {
    color: var(--primary-color);
}

.cart {
    position: fixed;
    right: 0;
    height: 100vh;
    width: 500px;
    z-index: 1001;
    background-color: #fff;
    box-shadow: var(--box-shadow);
    /* padding: 2em; */
    transform: translateX(100%);
    display: flex;
    flex-direction: column;
    background-color: #eee;
    --cart-padding: 15px;
}

.cart.cart--display {
    transform: translateX(0);
}

.cart__table {
    flex-grow: 1;
    overflow: auto;
    /* padding-top: 10px; */
    padding: var(--cart-padding);
}

.cart__table::-webkit-scrollbar {
    width: 14px;
    height: 14px;
}

.cart__table::-webkit-scrollbar-thumb {
    height: 6px;
    border: 4px solid rgba(0, 0, 0, 0);
    background-clip: padding-box;
    -webkit-border-radius: 7px;
    background-color: rgba(0, 0, 0, 0.5);
    -webkit-box-shadow: inset -1px -1px 0px rgba(0, 0, 0, 0.05),
        inset 1px 1px 0px rgba(0, 0, 0, 0.05);
}

.cart__table::-webkit-scrollbar-button {
    width: 0;
    height: 0;
    display: none;
}

.cart__table::-webkit-scrollbar-corner {
    background-color: transparent;
}

.cart__title {
    margin-bottom: 1.5em;
    position: relative;
    flex: 0 0 auto;
    padding: var(--cart-padding);
    background-color: #fff;
    box-shadow: var(--box-shadow);
}

.cart__close-icon {
    position: absolute;
    top: 50%;
    right: 0;
    transform: translateY(-50%);
    color: var(--gray) !important;
    cursor: pointer;
}
.cart-table__titles {
    margin-bottom: 2em;
}

.cart .cart-table__item {
    margin-bottom: 0;
}

.cart-table__delete-icon {
    position: absolute;
    left: -10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
}

.cart__footer {
    display: flex;
    flex-direction: row;
    align-items: center;
    /* position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 1.5em; */
    box-shadow: var(--box-shadow);
    background-color: #fff;
    flex: 0 0 auto;
    padding: var(--cart-padding);
}

.cart-table__total {
    margin-bottom: 0;
}

.cart-table__total > span {
    color: var(--black);
}

.cart__button {
    text-align: right;
}

/* ======================= mobile ================================= */

/* =========================================================================================

Shop Page

=========================================================================================== */

.shop-section {
    margin-bottom: 20px;
}

.portal-table {
    /* --width: 1200px;
    max-width: var(--width); */
}

.portal-table__header {
    width: 100%;
}

.portal-table__header {
    margin-bottom: 3em;
}

.portal-table__titles {
    display: flex;
    flex-direction: row;
    /* margin-bottom: 1.5em; */
}

.portal-table__container {
    /* display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    justify-content: space-between; */
}

.portal-table__container.portal-table__container--shadow .portal-table__titles,
.portal-table__container.portal-table__container--shadow .portal-table__item {
    padding: 0em 0em 1em 0em;
}

.portal-table__item {
    border-radius: 20px;
    border: none;
    margin-bottom: 1em;
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
}

.portal-table__container--shadow .portal-table__item {
    background: #ffffff;
    /* box-shadow: 13px 13px 26px #d9d9d9, -13px -13px 26px #ffffff; */
}
.portal-table__column > input {
    border: none;
    border-radius: 30px;
    height: 100%;
    padding: 0 1em;
    background-color: var(--bg-color);
    height: 40px;
}

.portal-table__column2 {
    width: 100%;
}

.portal-table__product {
    color: var(--black);
    font-weight: 600;
}

.portal-unit__unit {
    position: relative;
}

.unit__arrow {
    position: absolute;
    top: 50%;
    right: 30px;
    transform: translateY(-50%);
}

.portal-table__quantity {
    /* display: flex;
    flex-direction: row;
    align-items: center; */
    justify-content: space-between;
    position: relative;
}

.portal-table__button {
    display: flex;
    justify-content: center;
}

.portal-table__button button {
    flex: 1;
}

.portal-table__branch select {
    border: none;
    background-color: transparent;
}

.portal-table__status {
    color: var(--danger);
}

.ui-selectmenu-button {
    background-color: var(--bg-color) !important;
    border-radius: 30px;
    border: none !important;
    width: 100% !important;
    height: 100% !important;
    display: flex;
    align-items: center;
    flex-direction: row-reverse;
    justify-content: flex-end;
    padding-left: 1.5em;
    padding-right: 1.5em;
}

.ui-selectmenu-icon {
    display: none;
}

.ui-selectmenu-button:focus,
.portal-table input:focus {
    outline-color: transparent !important;
}

/* =========================================================================================

Product Item

=========================================================================================== */

.product-item {
    width: 100%;
    background-color: #fff;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 15px;
    height: 94%;
    position: relative;
    padding-bottom: 40px;
    display: block;
}

.product-item:hover {
    box-shadow: var(--box-shadow);
    position: relative;
    top: -2px;
    transition: 0.2s;
}

.product-image {
    width: 100%;
    position: relative;
    overflow: hidden;
    padding-bottom: 100%;
    /* background-size: cover;
    background-position: center;
    background-repeat: no-repeat; */
}

.product-image > img {
    width: 100%;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.product-content {
    padding: 10px;
}

.product-title,
.product-detail {
    font-size: 12px;
}

.product-title {
    font-weight: 700;
    margin-bottom: 0;
}

.product-price {
    font-weight: 700;
    font-size: 13px;
    color: var(--primary-color);
}

.product-sales {
    font-size: 12px;
}

.product-footer {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
}

.product-footer-content {
    padding: 7px 10px !important;
}

.product-button {
    background-color: #b1b1b1;
    width: 100%;
    padding: 7px 15px;
    border-radius: 0 !important;
    border-top-left-radius: 10px !important;
    font-size: 10px;
    height: 100%;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    white-space: nowrap;
}

.product-button:hover {
    background-color: var(--primary-color);
    color: #fff;
}

.product-button > i {
    color: inherit;
}

.product-button-disabled {
    background-color: #b1b1b1;
    width: 100%;
    padding: 7px 15px;
    border-radius: 0 !important;
    border-top-left-radius: 10px !important;
    font-size: 10px;
    height: 100%;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    white-space: nowrap;
}

/* =========================================================================================

Single Product Page

=========================================================================================== */

/* .single-product-container {
    padding-top: 100px;
} */

/* .single-product-image {
    margin-bottom: 20px;
} */

.single-product-title {
    font-weight: 900;
}

.single-product-price {
    font-weight: 900;
    color: var(--primary-color);
}

.single-product-summary,
.single-product-full-detail {
    background-color: #fff;
    margin-top: 20px;
    padding: 20px;
    border-radius: 5px;
}

.summary-title {
    color: grey;
    font-size: 15px;
}

.single-product-summary .summary-content {
    font-weight: 700;
    font-size: 17px;
}

.single-product-pack {
    color: grey;
} 

.single-product-quantity {
    padding: 20px;
}

.single-product-quantity .input-group {
    width: 150px;
}

.single-product-quantity .btn {
    padding: 5px 10px;
    border-radius: 5px;
}

.single-product-buttons {
    margin-top: 20px;
}

.single-product-buttons .btn {
    font-weight: 900;
    font-size: 15px;
    padding: 10px 40px;
    transition: 0.3s;
}

.single-product-buttons .btn:hover {
    opacity: 0.6;
}

.single-product-buttons .btn-primary {
    border: 2px solid var(--primary-color);
    background-color: var(--primary-color);
    color: #fff;
}

.single-product-buttons .btn-secondary {
    border: 2px solid var(--primary-color);
    background-color: transparent;
    color: var(--primary-color);
}

.single-product-buttons .btn-disabled {
    border: 2px solid var(--gray);
    background-color: var(--gray);
    color: #fff;
}

.single-product-detail-title {
    font-weight: 900;
    margin-bottom: 30px;
}

/* a.myhover:hover {
    opacity: 0.6;
} */

.picture, .picture1, .picture2, .picture3, .picture4, .picture5, .picture6 {
    width: 100%;
    padding-bottom: 100%;
    overflow: hidden;
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    margin-bottom: 5px;
    border: 1px solid gainsboro;
}

.myhover-container {
    display: flex;
    /*justify-content: space-between;*/
}

.myhover {
    width: 18%;
    position: relative;
    padding-bottom: 18%;
    overflow: hidden;
    display: block;
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    border: 1px solid gainsboro;
}

.myhover:last-child {
    margin-right: 0;
}

/* =========================================================================================

Checkout Page

=========================================================================================== */

.receiver-detail-container {
    padding: 20px;
    border-radius: 5px;
    background-color: #fff;
}

.receiver__title {
    font-weight: 700;
}

.checkout__delete-icon {
    cursor: pointer;
}

.checkout__footer-container {
    /* position: fixed;
    bottom: 0;
    left: 0;
    right: 0; */
    padding: 9em 2em 0 calc(var(--sidemenu-wd) + 2em);
    /* width: 100%; */
}

.checkout__footer {
    /* background-color: #fff; */
    padding: 1.5em 1.5em 2.5em;
    /* box-shadow: var(--box-shadow); */
    border-radius: 5px;
    justify-content: space-between;
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    position: relative;
}

.checkout__subtotal {
    font-size: 13px;
}

.receiver__name {
    margin-bottom: 0.5em;
}

.toast-message {
    color: var(--bg-color) !important;
}

.receiver__name input {
    color: var(--black);
    font-weight: 700;
    border: none;
    border-radius: 30px;
    width: 100%;
    padding: 0.5em 1em;
}

.receiver__name p {
    color: var(--black);
    font-weight: 700;
}

.receiver__name textarea {
    color: var(--black);
    font-weight: 700;
    border: none;
    border-radius: 30px;
    width: 100%;
    padding: 0 1em;
    font-size: inherit !important;
}

.receiver__name input:invalid {
    border: 1px solid var(--danger);
}

.receiver__name select:invalid {
    border: 1px solid var(--danger);
}

input:focus {
    outline: none;
}

select:focus {
    outline: none;
}

.receiver__name select {
    border: none;
    color: var(--black);
    font-weight: 700;
    border-radius: 30px;
    padding: 0.5em 1em;
}

.receiver__name select:focus {
    outline-color: transparent !important;
}

.checkout__total span {
    color: var(--black);
    font-weight: 700;
}

.receiver__input {
    background-color: #fff;
}

/* .checkout__button {
    padding-left: 3em;
    padding-right: 3em;
} */

.checkout__footer .footer__header {
    color: var(--black);
    font-weight: 700;
    position: relative;
}

.checkout__footer--display-icon,
.checkout__footer--close-icon {
    /* font-size: 1.5em; */
    position: absolute;
    top: 0;
    right: 25px;
    z-index: 1003;
}

.checkout__footer--display-icon {
    display: none;
}

.checkout__footer--close .checkout__footer--display-icon {
    display: block;
}

.checkout__footer--close .checkout__footer--close-icon {
    display: none;
}

.checkout__footer .footer__header {
    cursor: pointer;
}

.select2-container--default .select2-selection--single {
    background-color: #eee !important;
    border: none !important;
    height: 38px !important;
    font-size: 12px !important;
}

.select2:invalid + .select2-container--default .select2-selection--single {
    border: 1px solid #ff0000 !important;
}

.select2-container--default .select2-selection--single {
    display: flex !important;
    align-items: center !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    top: 50% !important;
    transform: translateY(-50%) !important;
}

.detail-card {
    padding: 20px;
    background-color: #fff;
    border-radius: 5px;
    -webkit-box-shadow: 0px 3px 5px 0px rgba(0, 0, 0, 0.31);
    -moz-box-shadow: 0px 3px 5px 0px rgba(0, 0, 0, 0.31);
    box-shadow: 0px 3px 5px 0px rgba(0, 0, 0, 0.31);
    margin-bottom: 10px;
}

.detail-input {
    border-radius: 5px;
    border: none;
    background-color: #eee !important;
    width: 100%;
    padding: 10px;
}

.detail-input::placeholder {
    font-size: 12px;
    /* color: #ccc; */
}

.detail-input,
.detail-input option {
    font-size: 12px;
}

.detail-input:invalid {
    border: 1px solid #ff0000 !important;
}

.checkout-total-container {
    padding: 10px !important;
    display: flex;
    align-items: center;
}

.checkout-total {
    color: #222;
    font-family: var(--primary-font);
    font-weight: 900;
    display: flex;
    align-items: center;
}

.checkout-total .highlight > span {
    color: #222;
    font-size: 20px;
    font-family: var(--primary-font);
    font-weight: 800;
    margin-left: 10px;
}

.checkout-button {
    background-color: var(--primary-color);
    text-align: center;
    padding: 15px 40px;
    color: #fff;
    font-weight: 700;
    font-size: 12px;
}

.gray-button {
    background-color: #d3d3d3;
    text-align: center;
    padding: 15px 40px;
    color: #fff;
    font-weight: 700;
    font-size: 12px;
}

.checkout-estimate-date {
    font-weight: 700;
}

/* =========================================================================================

Order Page

=========================================================================================== */

.receive__button btn {
    background-color: var(--warning);
}

/* =========================================================================================

Inventry item Page

=========================================================================================== */

.inventory-item-page .portal-table__item {
    border-bottom: 1px solid gainsboro;
    border-radius: 0;
    padding-bottom: 1em;
}

.inventory-item-page .portal-table__titles {
    display: flex;
}

.item__details {
    padding: 1.5em;
    background-color: #fff;
    border-radius: 20px;
    box-shadow: var(--box-shadow);
}

.item__content {
    margin-bottom: 0;
    color: var(--black);
    font-weight: 700;
}

.item__content > span {
    position: relative;
    color: inherit;
}

.quantity__decrease {
    color: var(--danger);
}

.quantity__increase {
    color: var(--success);
}

.inventory-item-page .portal-table__date {
    color: var(--black);
    font-weight: 600;
}

.quantity-adjust__container {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    padding: 4em 2em 2em 2em;
    z-index: 1000000000000000000000000;
    display: none;
}

.quantity-adjust__container.quantity-adjust__container--display {
    display: block;
}

.quantity-adjust {
    text-align: center;
    padding: 1.3em;
    border-radius: 20px;
    box-shadow: var(--box-shadow);
    background-color: #fff;
    margin: 0 auto;
    max-width: 250px;
    display: flex;
    flex-direction: column;
    z-index: 200000;
    position: relative;
}

.quantity-adjust__close-icon {
    position: absolute;
    top: 1.5em;
    right: 1.5em;
    cursor: pointer;
}

.quantity-adjust--overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: -1;
}

.quantity-adjust__input {
    margin-bottom: 1.5em;
    border: none;
    border-radius: 30px;
    padding: 0.7em 1em;
    box-shadow: var(--box-shadow);
    text-align: center;
}

.quantity-adjust__icon {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
}

.quantity-adjust__icon-plus {
    right: -1.5em;
    /* color: var(--success); */
}

.quantity-adjust__icon-minus {
    left: -1.5em;
    /* color: var(--danger); */
}

.footer-socmed {
    font-size: 20px;
}

.portal-table__item {
    height: 100%;
    overflow: hidden;
}

.portal-table__image {
    position: relative;
    width: 100%;
    padding-bottom: 45%;
    overflow: hidden;
    margin-bottom: 15px;
    /* border-bottom-left-radius: 0;
    border-bottom-right-radius: 0; */
}

.portal-table__image > img {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.product__image {
    /* max-height: 16vh; */
    border-radius: 12px;
}

.order-item {
    background-color: #fff;
}

.product-card {
    background-color: #fff;
    padding: var(--padding);
    border-radius: 5px;
    -webkit-box-shadow: 0px 3px 5px 0px rgba(0, 0, 0, 0.31);
    -moz-box-shadow: 0px 3px 5px 0px rgba(0, 0, 0, 0.31);
    box-shadow: 0px 3px 5px 0px rgba(0, 0, 0, 0.31);
    --padding: 10px;
    margin-bottom: 20px;
    width: 100%;
}

.product-card.unserviceable {
    background-color: #e5e5e5;
}

/* .cart .product-card {
    -webkit-box-shadow: 0px 3px 5px 0px rgba(0, 0, 0, 0);
    -moz-box-shadow: 0px 3px 5px 0px rgba(0, 0, 0, 0);
    box-shadow: 0px 3px 5px 0px rgba(0, 0, 0, 0);
    border: 1px solid gainsboro;
} */

.product-card-header {
    border-bottom: 1px solid lightgrey;
    padding-bottom: var(--padding);
}

.product-card .company-image {
    width: 100%;
    padding-bottom: 100%;
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    border-radius: 50%;
    border: 1px solid #ececec;
}

.product-card .product-card-title {
    font-size: 12px;
    font-family: var(--primary-font);
    font-weight: 700;
    padding-left: var(--padding);
    -webkit-text-stroke-width: 0.5px; 
    -webkit-text-stroke-color: black; 
}

}

.cart .product-card .product-card-title {
    font-size: 13px;
}

.product-card-delete {
    text-align: right;
    font-size: 20px;
    position: relative;
}

.product-card-delete > i {
    color: var(--primary-color);
    font-size: 13px;
    position: absolute;
    top: 0;
    right: 0;
    cursor: pointer;
}

.product-card-delete.btn {
    font-size: 10px;
    border: 1px solid var(--gray);
    background-color: transparent;
    color: var(--gray);
}

/* .product-card-body {
    padding: 0 var(--padding);
} */

.product-card-item {
    padding-top: var(--padding);
    padding-bottom: var(--padding);
    border-bottom: 1px solid #eee;
}

/* .product-card-item:last-child {
    border-bottom: none;
} */

.product-card-image {
    width: 100%;
    padding-bottom: 100%;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    border-radius: 5px;
    border: 1px solid #ececec;
}

.product-card-name,
.product-card-price {
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 2px;
}

.product-card-price {
    text-align: right;
}

.cart .product-card-name,
.cart .product-card-price {
    font-size: 13px;
}

.product-card-quantity {
    color: grey;
    font-size: 12px;
}

.cart .product-card-quantity {
    font-size: 12px;
}

.product-card-content {
    padding-left: var(--padding) !important;
}

.product-card-footer-option {
    text-align: center;
    padding: var(--padding);
    border: 1px solid #ccc;
    border-radius: 5px;
}

.product-card-footer-option.option--active {
    border: 1px solid #ccc;
}

.product-card-footer-option * {
    font-size: 13px;
    line-height: 13px;
}

.product-card-footer {
    /* border-top: 1px solid #ccc; */
    padding-top: var(--padding);
}

.product-card-total * {
    font-size: 15px;
}

.product-quantity {
    padding-left: 10px;
    padding-right: 10px;
    margin-bottom: 10px;
}

.product-quantity * {
    font-size: 10px;
}

.product-quantity button {
    padding: 5px 10px;
    border-color: rgb(206, 212, 218);
    color: rgb(206, 212, 218);
}

.cartpage-footer {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: #fff;
    -webkit-box-shadow: 0px 3px 5px 0px rgba(0, 0, 0, 0.31);
    -moz-box-shadow: 0px 3px 5px 0px rgba(0, 0, 0, 0.31);
    box-shadow: 0px -1px 5px 0px rgba(0, 0, 0, 0.31);
}

.cartpage-button {
    background-color: var(--primary-color);
    text-align: center;
    padding: 15px 40px;
    color: #fff;
    font-weight: 700;
    font-size: 17px;
}

.cartpage-total-container {
    padding: 10px !important;
    display: flex;
    align-items: center;
}

.cartpage-total {
    color: #222;
    font-family: var(--primary-font);
    font-weight: 900;
    display: flex;
    align-items: center;
}

.cartpage-highlight {
    color: #222;
    font-size: 20px;
    font-family: var(--primary-font);
    font-weight: 800;
    margin-left: 10px;
}

@media only screen and (max-width: 480px) {
    .single-product-buttons .btn {
        padding-left: 0px;
        padding-right: 0px;
    }
}

.upper__content *{
    color: var(--header_upper_txtcolor);
}

.col-auto.ml-auto.d-flex.align-items-center.cartHolder i{
    color: var(--header_middle_icons);
}

/* Extra Small Devices, Phones */ 
@media only screen and (max-width : 480px) {
    .cartpage-highlight {
        font-size: 20px;
    }
    .checkout-total .highlight > span {
        font-size: 14px;
    }
    .checkout-button {
        padding: 15px 35px;
    }
}

@media only screen and (max-width : 355px) {
    .cartpage-highlight {
        font-size: 14px;
    }
}

@media only screen and (max-width : 319px) {
    .cartpage-highlight {
        font-size: 10px;
    }
}

@media only screen and (max-width : 337px) {
    .checkout-button {
        padding: 15px 20px;
    }
    .cartpage-highlight {
        font-size: 12px;
    }
}

@media only screen and (max-width : 319px) {
    .checkout-button {
        padding: 15px 20px;
    }
    .cartpage-highlight {
        font-size: 8px;
    }
}

@media only screen and (max-width : 375px) {
    .checkout-button {
        padding: 15px 20px;
    }
}

.receiver-detail-container span.select2-container--default {
    width: 100% !important;
}
</style>
<div class="checkout-page shop-container__web mb-5">
  <div class="portal-table col-12">
    <div class="row">

        <div class="col-lg-7 col-12 checkout__order mb-4">
            <div class="portal-table h-100">
                <h6 class="mb-4 receiver__title"><i class="fa fa-shopping-cart mr-3"></i>Your Orders</h6>
                <div id="checkoutPage">
                    <!-- Order Summary Here -->

                    <?php foreach($order_items as $sKey => $shop){
                        // $shop['order_status'] = $shop['order_status'];
                     ?>
                    <div class="product-card" id="product-card-list">
                        <div class="product-card-header">
                            <div class="row">
                                <div class="col">
                                    <div class="row no-gutters">
                                        <div class="col-1 d-flex align-items-center justify-content-end">
                                            <div><img 
                                                    class="img-thumbnail" 
                                                    style="width: 50px;" 
                                                    src="<?=shop_url()."assets/img/shops-60/".pathinfo($shop['logo'], PATHINFO_FILENAME).".jpg"?>"></div>
                                        </div>
                                        <div class="col d-flex align-items-center">
                                            <div class="product-card-title"><?=$shop['shopname'];?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if($order_details['payment_status'] == '1') {?>
                            <div class="row no-gutters mt-2">
                                <div class="col-12 col-md-8 offset-md-2">
                                    <div class="px-3 py-2 rounded" data-target="#order_status_child_collapse-<?=$shop['shopcode']?>" aria-expanded="false" aria-controls="order_status_child_collapse-<?=$shop['shopcode']?>" id="order_status_parent_collapse">
                                        <span class="product-card-name" style="font-size: 14px">Order Status</span>  <i class="fa fa-chevron-down float-right order-status-caret" hidden></i>
                                    </div>
                                    <div class="collapse col-12 col-md-12 py-3 px-4 show" id="order_status_child_collapse-<?=$shop['shopcode']?>">
                                        <div class="portal-timeline-vertical">
                                            <div class="portal-timeline-vertical__item portal-timeline__step-1 <?= in_array($shop['order_status'], ['p', 'po', 'rp', 'bc', 'f', 's']) ? 'portal-timeline-vertical__item--active' : '' ;?> ">
                                                <div class="portal-timeline-vertical__node">
                                                    <?php if (in_array($shop['order_status'], ['p', 'po', 'rp', 'bc', 'f', 's'])) { ?>
                                                    <i class="fa fa-check" aria-hidden="true" rel="tooltip">
                                                    </i>
                                                    <?php } ?>
                                                </div>
                                                <div class="portal-timeline-vertical__title">Order Placed</div>
                                                <div class="portal-timeline-vertical__timestamp text-right flex-grow-1" <?= in_array($shop['order_status'], ['p', 'po', 'rp', 'bc', 'f', 's']) ? '' : 'hidden' ;?> ><?=format_shortdatetime($shop['date_ordered'])?></div>
                                            </div>
                                            <div class="portal-timeline-vertical__item portal-timeline__step-2 <?= in_array($shop['order_status'], ['po', 'rp', 'bc', 'f', 's']) ? 'portal-timeline-vertical__item--active' : '' ;?>">
                                                <div class="portal-timeline-vertical__node">
                                                    <?php if (in_array($shop['order_status'], ['po', 'rp', 'bc', 'f', 's'])) { ?>
                                                    <i class="fa fa-check" aria-hidden="true" rel="tooltip">
                                                    </i>
                                                    <?php } ?>
                                                </div>
                                                <div class="portal-timeline-vertical__title">Processing</div>
                                                <div class="portal-timeline-vertical__timestamp text-right flex-grow-1" <?= in_array($shop['order_status'], ['po', 'rp', 'bc', 'f', 's']) ? '' : 'hidden' ;?> ><?=format_shortdatetime($shop['date_order_processed'])?></div>
                                            </div>
                                            <div class="portal-timeline-vertical__item portal-timeline__step-2 <?= in_array($shop['order_status'], ['rp', 'bc', 'f', 's']) ? 'portal-timeline-vertical__item--active' : '' ;?>">
                                                <div class="portal-timeline-vertical__node">
                                                    <?php if (in_array($shop['order_status'], ['rp', 'bc', 'f', 's'])) { ?>
                                                    <i class="fa fa-check" aria-hidden="true" rel="tooltip">
                                                    </i>
                                                    <?php } ?>
                                                </div>
                                                <div class="portal-timeline-vertical__title">Ready for Pickup</div>
                                                <div class="portal-timeline-vertical__timestamp text-right flex-grow-1" <?= in_array($shop['order_status'], ['rp', 'bc', 'f', 's']) ? '' : 'hidden' ;?> ><?=format_shortdatetime($shop['date_ready_pickup'])?></div>
                                            </div>
                                            <div class="portal-timeline-vertical__item portal-timeline__step-2 <?= in_array($shop['order_status'], ['bc', 'f', 's']) ? 'portal-timeline-vertical__item--active' : '' ;?>">
                                                <div class="portal-timeline-vertical__node">
                                                    <?php if (in_array($shop['order_status'], ['bc', 'f', 's'])) { ?>
                                                    <i class="fa fa-check" aria-hidden="true" rel="tooltip">
                                                    </i>
                                                    <?php } ?>
                                                </div>
                                                <div class="portal-timeline-vertical__title">Booking Confirmed</div>
                                                <div class="portal-timeline-vertical__timestamp text-right flex-grow-1" <?= in_array($shop['order_status'], ['bc', 'f', 's']) ? '' : 'hidden' ;?> ><?=format_shortdatetime($shop['date_booking_confirmed'])?></div>
                                            </div>
                                            <div class="portal-timeline-vertical__item portal-timeline__step-2 <?= in_array($shop['order_status'], ['f', 's']) ? 'portal-timeline-vertical__item--active' : '' ;?>">
                                                <div class="portal-timeline-vertical__node">
                                                    <?php if (in_array($shop['order_status'], ['f', 's'])) { ?>
                                                    <i class="fa fa-check" aria-hidden="true" rel="tooltip">
                                                    </i>
                                                    <?php } ?>
                                                </div>
                                                <div class="portal-timeline-vertical__title">On the Way</div>
                                                <div class="portal-timeline-vertical__timestamp text-right flex-grow-1" <?= in_array($shop['order_status'], ['f', 's']) ? '' : 'hidden' ;?> ><?=format_shortdatetime($shop['date_fulfilled'])?></div>
                                            </div>
                                            <div class="portal-timeline-vertical__item portal-timeline__step-3 <?= $shop['order_status'] == 's'  ? 'portal-timeline-vertical__item--active' : '' ;?>">
                                                <div class="portal-timeline-vertical__node">
                                                    <?php if ($shop['order_status'] == 's') { ?>
                                                    <i class="fa fa-check" aria-hidden="true" rel="tooltip" title="Key active" id="" style="
                                                        color: var(--primary-color);position: relative;top: -5px;left: 2px;font-size: 12px;">
                                                    </i>
                                                    <?php } ?>
                                                </div>
                                                <div class="portal-timeline-vertical__title">Shipped</div>
                                                <div class="portal-timeline-vertical__timestamp text-right flex-grow-1" <?= $shop['order_status'] == 's' ? '' : 'hidden' ;?> ><?=format_shortdatetime($shop['date_shipped'])?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } $subtotal = 0; 
                        foreach($shop['items'] as $order){ ?>
                            <div class="product-card-body">
                                <div class="product-card-item">
                                    <div class="row no-gutters">
                                        <div class="col">
                                            <div class="row no-gutters">
                                                <div class="col-2 col-md-1">
                                                    <div class="product-card-image" style="background-image: url(<?=shop_url()."assets/img/".$shop['shopcode']."/products-250/".$order['productid']."/0-".$order['productid'].".jpg"?>)"></div>
                                                </div>
                                                <div class="col product-card-content">
                                                    <div class="product-card-name">
                                                        <?=$order['itemname'];?>
                                                    </div>
                                                    <div class="product-card-quantity">
                                                        Quantity: <?=number_format($order['quantity'])?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-3 col-md-2 d-none d-md-block">
                                            <div class="product-card-price">
                                                <?=$order['unit'];?>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="product-card-price">
                                                ₱ <?=number_format($order['price'],2)?>
                                            </div>
                                        </div>
                                        <div class="col-1">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php $subtotal += ($order['price'] * $order['quantity']); } ?>

                        <div id="shipping-card-${shop.shopid}" class="product-card-footer">
                            <div class="product-card-footer-content container-fluid">
                                <div class="row">
                                    <div class="col-12 col-md">
                                        <div class="row d-flex justify-content-end">
                                            <div class="col col-md-6">
                                                <div class="pb-2 col-12 text-left product-card-title">
                                                    Shipping
                                                </div>
                                                <div class="product-card-footer-option option--active">
                                                    <div id="shippingfee-card-${shop.shopid}" class="font-weight-bold">
                                                    <?= $shop['shippingfee'] != 0 ? "₱ ".$shop['shippingfee'] : "Free Shipping";?>
                                                    </div>
                                                <?php if($shop['order_status'] != 's') { ?>
                                                    <?php if($shop['order_status'] != 'p') { ?>
                                                        <div class="">Delivered On: </div>
                                                        <div id="shippingdts-card-${shop.shopid}" class=""><?= date("F d", strtotime($order_details['payment_date']. ' + '.$shop['shopdts'].' days')).' to '.date("F d, Y", strtotime($order_details['payment_date']. ' + '.$shop['shopdts_to']. ' days')) ?></div>
                                                    <?php } else { ?>
                                                        <div class="">Estimated Delivery Date: </div>
                                                        <?php if($order_details['payment_status'] == '1') {?>
                                                        <div id="shippingdts-card-${shop.shopid}" class=""><?= date("F d", strtotime($order_details['payment_date']. ' + '.$shop['shopdts'].' days')).' to '.date("F d, Y", strtotime($order_details['payment_date']. ' + '.$shop['shopdts_to']. ' days')) ?></div>
                                                        <?php } else { ?>
                                                        <div id="shippingdts-card-${shop.shopid}" class=""><?= $shop['shopdts'].' to '.$shop['shopdts_to']. ' days' ?></div>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <div class="">Date Shipped: </div>
                                                    <div id="shippingdts-card-${shop.shopid}" class=""><?= date_format(date_create($shop['date_shipped']), 'm/d/Y');?></div>
                                                <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="product-card-body py-3 product-card-total">
                            <div class="product-card-item">
                                <div class="row no-gutters">
                                    <div class="col product-card-name text-right">
                                        Sub-total:
                                    </div>
                                    <div class="col-5 col-md-4">
                                        <div class="product-card-price" id="subtotal-card-${shop.shopid}">
                                            ₱ <?=number_format($subtotal,2)?>
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <div class="product-card-delete">
                                        </div>
                                    </div>
                                </div>
                                <?php if (isset($shop['vouchers']) && sizeof($shop['vouchers']) > 0) { ?>
                                <div id="applied-vouchers-section-<?=$sKey?>">
                                    <?php foreach($shop['vouchers'] as $key => $voucher) { ?>
                                    <div class="row no-gutters discount-item mb-1">
                                        <div class="col product-card-name applied-voucher-code text-md-right">
                                            <?= $key == 0 ? 'Voucher' : ''; ?>
                                        </div>
                                        <div class="col-11 col-md-4">
                                            <div class="inline-block pull-left">
                                                <span style="font-size: 10px; background-color: #eee;" class="badge badge-pill border p-2 ml-1">
                                                    <i style="font-size: inherit;" class="fa fa-tag mr-1" aria-hidden="true"></i>
                                                    <?= $voucher['vcode'] ?>
                                                </span>
                                            </div>
                                            <div class="inline-block pull-right total-discount-per-shop">
                                                <div class="product-card-price">
                                                    <?= '- ₱ '.number_format($voucher['vamount'],2) ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-1">
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                                <div class="row no-gutters" id="disc-holder-${shop.shopid}">
                                    <div hidden>
                                        <input id="disc-subtotal-value-${shop.shopid}" type="text" value="${parseFloat(subtotal)}">
                                    </div>
                                    <div class="col product-card-name text-md-right">
                                        New Sub-total:
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="product-card-price disc-sub-total-card" id="disc-subtotal-card-${shop.shopid}">
                                            <?= '₱ '.number_format(floatval($subtotal) - floatval($shop['voucherSubTotal']),2) ?>
                                        </div>
                                    </div>
                                    <div class="col-1">
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div> 
                    </div>
                    <?php } ?>
                </div>
                <div class="product-card">
                    <div class="product-card-body py-3 product-card-total">
                        <div class="row no-gutters mb-2">
                            <div class="col product-card-name text-right">
                                Sub-total
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="product-card-price" id="sub_total_amount_checkout">
                                    ₱ <?=number_format($order_details['order_total_amt'], 2, ".", ",");?>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="product-card-delete">
                                </div>
                            </div>
                        </div>
                        <?php if ($order_details['voucherAmount'] > 0): ?>
                        <div class="row no-gutters mb-2">
                            <div class="col product-card-name text-right">
                                Voucher
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="product-card-price" id="sub_total_amount_checkout">
                                   - ₱ <?=number_format($order_details['voucherAmount'], 2, ".", ",");?>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="product-card-delete">
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="row no-gutters mb-2">
                            <div class="col product-card-name text-right">
                                Shipping Fee <i class="fa fa-exclamation-circle" aria-hidden="true" rel="tooltip" title="Key active" id=""></i>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="product-card-price"  id="shipping_amount_checkout">
                                    ₱ <?=number_format($order_details['delivery_amount'], 2, ".", ",");?>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="product-card-delete">
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <h6 class="mb-4 receiver__title"><i class="fa fa-user mr-3"></i>Contact Details</h6>
            <div class="detail-card receiver-detail-container mb-4">
                
                <small>Email Address</small>
                <div class="mb-2">
                    <input disabled id="checkout_email" name="checkout_email" class="detail-input" type="email" placeholder="Email Address" value="<?=$order_details['email']?>">
                </div>
                <div class="mb-2"> <small style="color:var(--gray);">Note: Please check your email for order updates</small>
                </div>
            </div>
            <h6 class="mb-4 receiver__title"><i class="fa fa-truck mr-3"></i>Shipping Details</h6>
            <div class="detail-card receiver-detail-container mb-4">
                <small>Receiver's Name</small>
                <div class="mb-2">
                    <input disabled id="checkout_name" name="checkout_name" class="detail-input" type="text" placeholder="Receiver's Name" value="<?=ucwords(strtolower($order_details['fullname']));?>">
                </div>
                <small>Receiver's Mobile Number</small>
                <div class="mb-2">
                    <input disabled id="checkout_conno" name="checkout_conno" class="detail-input numberInput" type="text" placeholder="Receiver's Mobile Number" value="<?=$order_details['conno']?>">
                </div>
                <small>Shipping Address</small>
                <textarea disabled id="checkout_address" name="checkout_address" class="detail-input" name="" id="" cols="30" rows="2" placeholder="Address (House #, Street,Village)" ><?=$order_details['address']?></textarea>
                <?php if($order_details['notes'] != ''): ?>
                    <small>Landmark/Notes</small>
                    <textarea id="instructions" name="instructions" class="detail-input" name="" id="" cols="30" rows="3" placeholder="Landmarks/Notes"><?= $order_details['notes'] != "" ? $order_details['notes'] : "N/A";?></textarea>
                <?php endif;?>
            </div>
            <h6 class="mb-4 receiver__title"><i class="fa fa-tags mr-3"></i>Order Details</h6>
            <div class="detail-card receiver-detail-container">
                <small>Order Number</small>
                <div class="mb-2">
                    <input disabled id="checkout_refnum" name="checkout_refnum" class="detail-input" type="text" value="<?=$order_details['reference_num'];?>">
                </div>
                <?php if($order_details['payment_status'] == 1):?>
                    <small>Payment Reference No</small>
                    <div class="mb-2">
                        <input disabled id="checkout_payref" name="checkout_payref" class="detail-input" type="text" value="<?=$order_details['paypanda_ref'];?>">
                    </div>
                <?php endif;?>
                <small>Date of Purchase</small>
                <div class="mb-2">
                    <input disabled id="checkout_dop" name="checkout_dop" class="detail-input" type="text" placeholder="Receiver's Name" value="<?=date_format(date_create($order_details['date_ordered']), 'm/d/Y h:i:s A');?>">
                </div>
                <?php if($order_details['payment_status'] == 1): ?>
                    <small>Date of Payment</small>
                    <div class="mb-2">
                        <input disabled id="checkout_payment_date" name="checkout_payment_date" class="detail-input" type="text" placeholder="Receiver's Name" value="<?= $order_details['payment_date'] != "0000-00-00 00:00:00" ? date_format(date_create($order_details['payment_date']), 'm/d/Y h:i:s A') : "N/A";?>">
                    </div>
                <?php endif;?>
                <small>Payment Status</small>
                <div class="mb-2">
                    <?php if($status == 'Q') {?>
                        <input disabled id="checkout_paystatus_q" name="checkout_paystatus_q" class="detail-input font-weight-bold" type="text"  value="Processing Payment" style="color:var(--green)">
                    <?php } else if($order_details['payment_status'] == '0') {?>
                        <input disabled id="checkout_paystatus_0" name="checkout_paystatus_0" class="detail-input font-weight-bold" type="text" placeholder="Receiver's Name" value="Waiting for payment confirmation" style="color:var(--orange)">
                        <label>NOTE: All payment verification takes up to 24 hrs</label>
                    <?php } else if($order_details['payment_status'] == '1') {?>
                        <input disabled id="checkout_paystatus_1" name="checkout_paystatus_1" class="detail-input font-weight-bold" type="text"  value="Paid" style="color:var(--green)">
                    <?php } else if($order_details['payment_status'] == '2') {?>
                        <input disabled id="checkout_paystatus_2" name="checkout_paystatus_2" class="detail-input font-weight-bold" type="text"  value="Failed" style="color:var(--red)">
                    <?php } ?>
                </div>
            </div>
            
            <div class="detail-card checkout-footer">
                <div class="row no-gutters">
                    <div class="col checkout-total-container">
                        <div class="checkout-total">
                            Total: <span class="highlight" id="total_amount_checkout"> <span class='ml-2'>₱ <?=number_format((floatval($order_details['order_total_amt']) - floatval($order_details['voucherAmount']))+floatval($order_details['delivery_amount']), 2, ".", ",");?></span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>