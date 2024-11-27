<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>메디컬 아이템 | 로그인</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="<?= base_url('public/assets/adminLTE3/plugins/fontawesome-free/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/assets/adminLTE3/dist/css/adminlte.min.css?v=3.2.0') ?>">
</head>

<body class="hold-transition login-page">
<div class="login-box">
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <b class="h1">메디컬아이템</b>
        </div>
        <div class="card-body">
            <div class="input-group mb-3">
                <input type="text" class="form-control" id="adminId" placeholder="아이디">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-user"></span>
                    </div>
                </div>
            </div>
            <div class="input-group mb-3">
                <input type="password" class="form-control" id="adminPwd" placeholder="비밀번호">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="offset-8 col-4">
                    <button type="button" class="btn btn-primary btn-block" id="loginBtn">로그인</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('public/assets/adminLTE3/plugins/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('public/assets/adminLTE3/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('public/assets/adminLTE3/dist/js/adminlte.min.js?v=3.2.0') ?>"></script>
<script src="<?= base_url('public/assets/js/UtUtils.js')?>"></script>

<script>

    $(function () {

        // 로그인 버튼 클릭
        $('#loginBtn').click(function () {
            let adminId = $('#adminId').val();
            let adminPwd = $('#adminPwd').val();

            if (adminId === '' || adminId === null) {
                alert('아이디를 확인해주세요.');
                return false;
            }

            if (adminPwd === '' || adminPwd === null) {
                alert('비밀번호를 확인해주세요.');
                return false;
            }

            let data = {
                'adminId' : adminId,
                'adminPwd' : adminPwd
            };

            console.log('111');

            callAjax(
                'login',
                JSON.stringify(data),

                (resData) => {
                    console.log(resData);
                    console.log('222');

                    switch (resData.statusCode) {
                        case 'statOK':
                            alert('환영합니다.');
                            location.href = 'product';

                            break;

                        case 'ERROR':
                            let errMsg = resData.statusValue;
                            alert(errMsg);
                            break;

                        default:
                            console.log('login Ajax Default Error');
                            break;
                    }
                }
            );
        });

    });
</script>

</body>
</html>
