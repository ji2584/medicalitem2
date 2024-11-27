<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>메디컬아이템 | 관리자 페이지</title>

    <?= $this->include('layout/common_css'); ?>
    <?= $this->renderSection('css'); ?>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

    <!-- NavBar -->
    <?= $this->include('layout/navbar'); ?>

    <!-- Main Sidebar Container -->
    <?= $this->include('layout/sidebar'); ?>

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <?= $this->renderSection('content-header') ?>

        <!-- Main content -->
        <?= $this->renderSection('content') ?>

    </div>
    <!-- /.content-wrapper -->

    <?= $this->include('layout/footer') ?>

</div>
<!-- ./wrapper -->

<?= $this->include('layout/common_js') ?>
<?= $this->renderSection('script') ?>
</body>
</html>
