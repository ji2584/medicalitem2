<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link">
        <img src="<?= base_url('public/assets/adminLTE3/dist/img/AdminLTELogo.png') ?>" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">메디컬 아이템</span>
    </a>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?= base_url('public/assets/adminLTE3/dist/img/user2-160x160.jpg') ?>" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">
                    <?= $session?></a>
            </div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="<?= base_url()?>product" class="nav-link">
                        <i class="fas fa-box"></i>
                        <p>상품 조회 기능</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url()?>sales" class="nav-link">
                        <i class="fas fa-box"></i>
                        <p>매출현황 Sales</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url()?>overview" class="nav-link">
                        <i class="fas fa-box"></i>
                        <p>통계</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url()?>hospital" class="nav-link">
                        <i class="fas fa-box"></i>
                        <p>병원 통계</p>
                    </a>
                </li>



        </nav>
    </div>
</aside>

<!-- jQuery -->
<script src="<?= base_url('public/assets/adminLTE3/plugins/jquery/jquery.min.js') ?>"></script>
<script>
    /*** add active class and stay opened when selected ***/
    let url = window.location;

    // for sidebar menu entirely but not cover treeview
    // 전체를 가져와서 조건에 맞는 경우만 addClass 를 실행하는 듯 하다...
    $('ul.nav-sidebar a').filter(function() {
        // console.log (this.href);
        // console.log (url);
        if (this.href) {
            return this.href == url;
            // return this.href == url || url.href.indexOf(this.href) == 0;
        }
    }).addClass('active');

    // for the treeview
    // parentsUtil 선택자에 해당하는 요소 바로 이전까지의 모든 요소만을 선택
    $('ul.nav-treeview a').filter(function() {
        if (this.href) {
            return this.href == url || url.href.indexOf(this.href) == 0;
        }
    }).parentsUntil('.nav-sidebar > .nav-treeview').addClass('menu-open').prev('a').addClass('active');
</script>