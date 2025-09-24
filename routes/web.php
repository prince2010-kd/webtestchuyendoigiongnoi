<?php
use App\Http\Controllers\backend\CategoryController;
use App\Http\Controllers\backend\NhomQuyenController;
use App\Http\Controllers\backend\QuestionOptionController;
use App\Http\Controllers\backend\PostsController;
use App\Http\Controllers\backend\user\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\backend\PhanQuyenController;
use App\Http\Controllers\backend\SmtpSettingController;
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\backend\ActivityLogController;
use App\Http\Controllers\backend\CauHinhChungController;
use App\Http\Controllers\backend\CauHinhSeoController;
use App\Http\Controllers\backend\ChuongTrinhAnhNguController;
use App\Http\Controllers\backend\CourseController;
use App\Http\Controllers\backend\DangKyTuVanController;
use App\Http\Controllers\backend\DanhMucBaiVietController;
use App\Http\Controllers\backend\GeneralController;
use App\Http\Controllers\backend\GiaoVienController;
use App\Http\Controllers\backend\HinhAnhHdongController;
use App\Http\Controllers\backend\KtraChatLuongController;
use App\Http\Controllers\backend\LevelController;
use App\Http\Controllers\backend\MenuFrontendController;
use App\Http\Controllers\backend\QlyBluanBlogController;
use App\Http\Controllers\backend\QlyBluanKinhNghiemHocController;
use App\Http\Controllers\backend\QlyBluanSuKienController;
use App\Http\Controllers\backend\QlyBluanTinTucController;
use App\Http\Controllers\backend\QlyBluanUuDaiController;
use App\Http\Controllers\backend\QuanLyKHController;
use App\Http\Controllers\backend\QuestionController;
use App\Http\Controllers\backend\QuestionSetController;
use App\Http\Controllers\backend\SanPhamController;
use App\Http\Controllers\backend\SliderController;
use App\Http\Controllers\backend\SpeechToTextController;
use App\Http\Controllers\backend\StageController;
use App\Http\Controllers\backend\TargetController;
use App\Http\Controllers\backend\ThanhCongController;
use App\Http\Controllers\backend\VideoController;

use UniSharp\LaravelFilemanager\Lfm;


    Route::get('/backend', [UserController::class, 'showLogin'])->name('login');

    Route::post('/admin/login', [UserController::class, 'login']);
    Route::post('/refresh-token', [UserController::class, 'refresh'])->name('admin.refresh.token');
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
    Route::get('/admin/account/edit', [UserController::class, 'editOwnAccount'])->name('admin.account.edit');
    Route::prefix('admin')
    // ->middleware('auth')
// ->middleware('check.refresh.tkn')
// ->middleware('url_permission')
    ->middleware([
        'check.refresh.tkn',
        'url_permission'
    ])
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('user.dashboard');
        })->name('admin.dashboard');

        Route::prefix('/list-user')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('admin.user.list');
            Route::get('/create', [UserController::class, 'create'])->name('admin.user.create');
            Route::post('/store', [UserController::class, 'store'])->name('admin.user.store');
            Route::get('/{id}/edit', [UserController::class, 'edit'])->name('admin.user.edit');
            Route::put('/{id}', [UserController::class, 'update'])->name('admin.user.update');
            Route::delete('/delete-multiple', [UserController::class, 'deleteMultiple'])->name('admin.user.deleteMultiple');
            Route::delete('/{id}', [UserController::class, 'destroy'])->name('admin.user.destroy');
            Route::put('/{id}/toggle-active', [UserController::class, 'toggleStatus'])->name('admin.user.toggleActive');
        });

        //danhsachmenu
        Route::get('/list-menu', [PhanQuyenController::class, 'index'])->name('admin.phanquyen.list');
        Route::get('/list-menu/create', [PhanQuyenController::class, 'create'])->name('admin.phanquyen.create');
        Route::post('/list-menu/store', [PhanQuyenController::class, 'store'])->name('admin.phanquyen.store');
        Route::get('/list-menu/{id}/edit', [PhanQuyenController::class, 'edit'])->name('admin.phanquyen.edit');
        Route::put('/list-menu/{id}', [PhanQuyenController::class, 'update'])->name('admin.phanquyen.update');
        Route::delete('/list-menu/{id}', [PhanQuyenController::class, 'destroy'])->name('admin.phanquyen.destroy');
        Route::get('/list-menu/{id}', [PhanQuyenController::class, 'show'])->name('admin.phanquyen.show');
        Route::put('/list-menu/{id}/update-stt', [PhanQuyenController::class, 'updateStt'])->name('admin.phanquyen.updateStt');
        Route::put('/list-menu/{id}/toggle-active', [PhanQuyenController::class, 'thaydoitrangthai']);

        // Category
        Route::prefix('list-category')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('admin.category.list');
            Route::get('/create', [CategoryController::class, 'create'])->name('admin.category.create');
            Route::post('/store', [CategoryController::class, 'store'])->name('admin.category.store');
            Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('admin.category.edit');
            Route::put('/{id}', [CategoryController::class, 'update'])->name('admin.category.update');
            Route::delete('/delete-multiple', [CategoryController::class, 'deleteMultiple'])->name('admin.category.deleteMultiple');
            Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('admin.category.destroy');
            Route::put('/{id}/toggle-active', [CategoryController::class, 'thaydoitrangthai'])->name('admin.category.toggleActive');
            Route::put('/{id}/update-stt', [CategoryController::class, 'updateStt'])->name('admin.category.updateStt');

        });
        // Posts
        Route::prefix('list-posts')->controller(PostsController::class)->group(function () {
            Route::get('/', 'index');        // GET /list-posts
            Route::post('/', 'store');       // POST /list-posts
            Route::put('/{id}', 'update');   // PUT /list-posts/{id}
            Route::delete('/{id}', 'destroy'); // DELETE /list-posts/{id}
            Route::get('/search', 'search'); // GET /list-posts/search
        });

        // Groups view
        Route::get('/list-groups', function () {
            return view('groups.index');
        })->name('admin.groups.index');

        //Nhom quyen
        Route::prefix('nhomquyen')->group(function () {
            Route::get('/demo', [NhomQuyenController::class, 'demo'])->name('nhomquyen.list');
            Route::get('', [NhomQuyenController::class, 'index'])->name('nhomquyen.list');
            Route::get('/create', [NhomQuyenController::class, 'create'])->name('nhomquyen.create');
            Route::post('/store', [NhomQuyenController::class, 'store'])->name('nhomquyen.store');
            Route::get('/{id}/edit', [NhomQuyenController::class, 'edit'])->name('nhomquyen.edit');
            Route::put('/{id}/update', [NhomQuyenController::class, 'update'])->name('nhomquyen.update');
            Route::delete('/{id}', [NhomQuyenController::class, 'destroy'])->name('nhomquyen.destroy');
            Route::put('/{id}/toggle-active', [NhomQuyenController::class, 'toggleStatus'])->name('nhomquyen.toggle.active');
            Route::put('/{id}/update-stt', [NhomQuyenController::class, 'updateStt'])->name('nhomquyen.update.stt');
        });

        //mail server smtp
        Route::prefix('list-smtp')->group(function () {
            Route::get('', [SmtpSettingController::class, 'index'])->name('admin.smtp_settings.list');
            Route::get('/create', [SmtpSettingController::class, 'create'])->name('admin.smtp_settings.create');
            Route::post('/store', [SmtpSettingController::class, 'store'])->name('admin.smtp_settings.store');
            Route::get('/edit/{id}', [SmtpSettingController::class, 'edit'])->name('admin.smtp_settings.edit');
            Route::put('/update/{id}', [SmtpSettingController::class, 'update'])->name('admin.smtp_settings.update');
            Route::delete('/{id}', [SmtpSettingController::class, 'destroy'])->name('admin.smtp_settings.destroy');
            Route::get('/{id}', [SmtpSettingController::class, 'show'])->name('admin.smtp_settings.show');
        });

        //mail server smtp
        Route::prefix('list-configmails')->group(function () {
            Route::get('', [GeneralController::class, 'index'])->name('admin.configmails.list');
            Route::get('/create', [GeneralController::class, 'create'])->name('admin.configmails.create');
            Route::post('/store', [GeneralController::class, 'store'])->name('admin.configmails.store');
            Route::get('/edit/{id}', [GeneralController::class, 'edit'])->name('admin.configmails.edit');
            Route::put('/update/{id}', [GeneralController::class, 'update'])->name('admin.configmails.update');
            Route::delete('/{id}', [GeneralController::class, 'destroy'])->name('admin.configmails.destroy');
            Route::get('/{id}', [GeneralController::class, 'show'])->name('admin.configmails.show');
            Route::put('/updatestatus/{id}', [GeneralController::class, 'updatestatus'])->name('admin.configmails.updatestatus');
        });

        //cauhinhchung
        Route::prefix('list-general')->group(function () {
            Route::get('/', [CauHinhChungController::class, 'edit'])->name('cauhinhchung.edit');
            Route::post('/store', [CauHinhChungController::class, 'store'])->name('cauhinhchung.store');
        });

        //cauhinhseo
        Route::prefix('list-seo')->group(callback: function () {
            Route::get('/', [CauHinhSeoController::class, 'index'])->name('admin.cauhinhseo.list');
            Route::get('/create', [CauHinhSeoController::class, 'create'])->name('admin.cauhinhseo.create');
            Route::post('/store', [CauHinhSeoController::class, 'store'])->name('admin.cauhinhseo.store');
            Route::get('/{id}/edit', [CauHinhSeoController::class, 'edit'])
                    ->where('id', '.*')  // Cho phép nhận tất cả chuỗi, kể cả có dấu /
                    ->name('admin.cauhinhseo.edit');
            Route::put('/{id}', [CauHinhSeoController::class, 'update'])->name('admin.cauhinhseo.update');
            Route::delete('/delete-multiple', [CauHinhSeoController::class, 'deleteMultiple'])->name('admin.cauhinhseo.deleteMultiple');
            Route::delete('/{id}', [CauHinhSeoController::class, 'destroy'])->name('admin.cauhinhseo.destroy');
            Route::put('/{id}/toggle-active', [CauHinhSeoController::class, 'thaydoitrangthai'])->name('admin.cauhinhseo.toggleActive');
            Route::put('/{id}/update-stt', [CauHinhSeoController::class, 'updateStt'])->name('admin.cauhinhseo.updateStt');
        });

        //danhmucbaiviet
        Route::prefix('list-article')->group(function () {
            Route::get('/', [DanhMucBaiVietController::class, 'index'])->name('admin.danhmucbaiviet.list');
            Route::get('/create', [DanhMucBaiVietController::class, 'create'])->name('admin.danhmucbaiviet.create');
            Route::post('/store', [DanhMucBaiVietController::class, 'store'])->name('admin.danhmucbaiviet.store');
            Route::get('/{id}/edit', [DanhMucBaiVietController::class, 'edit'])->name('admin.danhmucbaiviet.edit');
            Route::put('/{id}', [DanhMucBaiVietController::class, 'update'])->name('admin.danhmucbaiviet.update');
            Route::delete('/delete-multiple', [DanhMucBaiVietController::class, 'deleteMultiple'])->name('admin.danhmucbaiviet.deleteMultiple');
            Route::delete('/{id}', [DanhMucBaiVietController::class, 'destroy'])->name('admin.danhmucbaiviet.destroy');
            Route::put('/{id}/toggle-active', [DanhMucBaiVietController::class, 'thaydoitrangthai'])->name('admin.danhmucbaiviet.toggleActive');
            Route::put('/{id}/update-stt', [DanhMucBaiVietController::class, 'updateStt'])->name('admin.danhmucbaiviet.updateStt');
            Route::put('/{id}/toggle-is_featured', [DanhMucBaiVietController::class, 'thaydoinoibat'])->name('admin.danhmucbaiviet.togglefeatured');
        });

        //slider
        Route::prefix('list-slider')->group(function () {
            Route::get('/', [SliderController::class, 'index'])->name('admin.slider.list');
            Route::get('/create', [SliderController::class, 'create'])->name('admin.slider.create');
            Route::post('/store', [SliderController::class, 'store'])->name('admin.slider.store');
            Route::get('/{id}/edit', [SliderController::class, 'edit'])->name('admin.slider.edit');
            Route::put('/{id}', [SliderController::class, 'update'])->name('admin.slider.update');
            Route::delete('/delete-multiple', [SliderController::class, 'deleteMultiple'])->name('admin.slider.deleteMultiple');
            Route::delete('/{id}', [SliderController::class, 'destroy'])->name('admin.slider.destroy');
            Route::put('/{id}/toggle-active', [SliderController::class, 'thaydoitrangthai'])->name('admin.slider.toggleActive');
            Route::put('/{id}/update-stt', [SliderController::class, 'updateStt'])->name('admin.slider.updateStt');
        });

        //level
        Route::prefix('list-level')->group(function () {
            Route::get('', [LevelController::class, 'index'])->name('admin.level.list');
            Route::get('/create', [LevelController::class, 'create'])->name('admin.level.create');
            Route::post('/store', [LevelController::class, 'store'])->name('admin.level.store');
            Route::get('/edit/{id}', [LevelController::class, 'edit'])->name('admin.level.edit');
            Route::put('/update/{id}', [LevelController::class, 'update'])->name('admin.level.update');
            Route::delete('/{id}', [LevelController::class, 'destroy'])->name('admin.level.destroy');
            Route::get('/{id}', [LevelController::class, 'show'])->name('admin.level.show');
            Route::put('/updatestatus/{id}', [LevelController::class, 'updatestatus'])->name('admin.level.updatestatus');
        });

        //target
        Route::prefix('list-target')->group(function () {
            Route::get('', [TargetController::class, 'index'])->name('admin.target.list');
            Route::get('/create', [TargetController::class, 'create'])->name('admin.target.create');
            Route::post('/store', [TargetController::class, 'store'])->name('admin.target.store');
            Route::get('/edit/{id}', [TargetController::class, 'edit'])->name('admin.target.edit');
            Route::put('/update/{id}', [TargetController::class, 'update'])->name('admin.target.update');
            Route::delete('/{id}', [TargetController::class, 'destroy'])->name('admin.target.destroy');
            Route::get('/{id}', [TargetController::class, 'show'])->name('admin.target.show');
            Route::put('/updatestatus/{id}', [TargetController::class, 'updatestatus'])->name('admin.target.updatestatus');
        });

        //stage
        Route::prefix('list-stage')->group(function () {
            Route::get('', [StageController::class, 'index'])->name('admin.stage.list');
            Route::get('/create', [StageController::class, 'create'])->name('admin.stage.create');
            Route::post('/store', [StageController::class, 'store'])->name('admin.stage.store');
            Route::get('/edit/{id}', [StageController::class, 'edit'])->name('admin.stage.edit');
            Route::put('/update/{id}', [StageController::class, 'update'])->name('admin.stage.update');
            Route::delete('/{id}', [StageController::class, 'destroy'])->name('admin.stage.destroy');
            Route::get('/{id}', [StageController::class, 'show'])->name('admin.stage.show');
            Route::put('/updatestatus/{id}', [StageController::class, 'updatestatus'])->name('admin.stage.updatestatus');
        });

        //course
        Route::prefix('list-course')->group(function () {
            Route::get('', [CourseController::class, 'index'])->name('admin.course.list');
            Route::get('/create', [CourseController::class, 'create'])->name('admin.course.create');
            Route::post('/store', [CourseController::class, 'store'])->name('admin.course.store');
            Route::get('/edit/{id}', [CourseController::class, 'edit'])->name('admin.course.edit');
            Route::put('/update/{id}', [CourseController::class, 'update'])->name('admin.course.update');
            Route::delete('/{id}', [CourseController::class, 'destroy'])->name('admin.course.destroy');
            Route::get('/{id}', [CourseController::class, 'show'])->name('admin.course.show');
            Route::put('/updatestatus/{id}', [CourseController::class, 'updatestatus'])->name('admin.course.updatestatus');
        });

        // Giáo viên
        Route::prefix('list-teachers')->group(function () {
            Route::get('/', [GiaoVienController::class, 'index'])->name('admin.giaovien.list');
            Route::get('/create', [GiaoVienController::class, 'create'])->name('admin.giaovien.create');
            Route::post('/store', [GiaoVienController::class, 'store'])->name('admin.giaovien.store');
            Route::get('/{id}/edit', [GiaoVienController::class, 'edit'])->name('admin.giaovien.edit');
            Route::put('/{id}', [GiaoVienController::class, 'update'])->name('admin.giaovien.update');
            Route::delete('/delete-multiple', [GiaoVienController::class, 'deleteMultiple'])->name('admin.giaovien.deleteMultiple');
            Route::delete('/{id}', [GiaoVienController::class, 'destroy'])->name('admin.giaovien.destroy');
            Route::put('/{id}/toggle-active', [GiaoVienController::class, 'thaydoitrangthai'])->name('admin.giaovien.toggleActive');
            Route::put('/{id}/update-stt', [GiaoVienController::class, 'updateStt'])->name('admin.giaovien.updateStt');
        });

        // Khóa học
        Route::prefix('list-khoahoc')->group(function () {
            Route::get('', [QuanLyKHController::class, 'index'])->name('admin.khoahoc.list');
            Route::get('/create', [QuanLyKHController::class, 'create'])->name('admin.khoahoc.create');
            Route::post('/store', [QuanLyKHController::class, 'store'])->name('admin.khoahoc.store');
            Route::get('/edit/{id}', [QuanLyKHController::class, 'edit'])->name('admin.khoahoc.edit');
            Route::put('/update/{id}', [QuanLyKHController::class, 'update'])->name('admin.khoahoc.update');
            Route::delete('/delete-multiple', [QuanLyKHController::class, 'deleteMultiple'])->name('admin.khoahoc.deleteMultiple');
            Route::delete('/{id}', [QuanLyKHController::class, 'destroy'])->name('admin.khoahoc.destroy');
            Route::put('/{id}/toggle-active', [QuanLyKHController::class, 'thaydoitrangthai'])->name('admin.menufrontend.toggleActive');
            // Route::put('/updatestatus/{id}', [QuanLyKHController::class, 'updatestatus'])->name('admin.khoahoc.updatestatus');
        });

        // Menu web chính
        Route::prefix('list-menu-frontend')->group(function () {
            Route::get('', [MenuFrontendController::class, 'index'])->name('admin.menufrontend.list');
            Route::get('/create', [MenuFrontendController::class, 'create'])->name('admin.menufrontend.create');
            Route::post('/store', [MenuFrontendController::class, 'store'])->name('admin.menufrontend.store');
            Route::get('/edit/{id}', [MenuFrontendController::class, 'edit'])->name('admin.menufrontend.edit');
            Route::put('/update/{id}', [MenuFrontendController::class, 'update'])->name('admin.menufrontend.update');
            Route::delete('/delete-multiple', [MenuFrontendController::class, 'deleteMultiple'])->name('admin.menufrontend.deleteMultiple');
            Route::delete('/{id}', [MenuFrontendController::class, 'destroy'])->name('admin.menufrontend.destroy');
            Route::get('/{id}', [MenuFrontendController::class, 'show'])->name('admin.menufrontend.show');
            Route::put('/{id}/toggle-active', [MenuFrontendController::class, 'thaydoitrangthai'])->name('admin.menufrontend.toggleActive');
            Route::put('/{id}/update-stt', [MenuFrontendController::class, 'updateStt'])->name('admin.menufrontend.updatestatus');
        });

        // Quản lý danh sách đăng ký tư vấn
        Route::prefix('list-register')->group(function () {
            Route::get('', [DangKyTuVanController::class, 'index'])->name('admin.dangkytuvan.list');
            Route::post('/store', [DangKyTuVanController::class, 'store'])->name('admin.dangkytuvan.store');
        });

        // Quản lý bình luận bài viết tin tức
        Route::prefix('list-comment-news')->group(function () {
            Route::get('/', [QlyBluanTinTucController::class, 'index'])->name('admin.qlybluantintuc.list');
            Route::get('/{post}/comments', [QlyBluanTinTucController::class, 'comments'])->name('admin.qlybluantintuc.comments');
            Route::post('/comment/{comment}/approve', [QlyBluanTinTucController::class, 'approve'])->name('admin.qlybluantintuc.comment.approve');
            Route::post('/comment/{comment}/unapprove', [QlyBluanTinTucController::class, 'unapprove'])->name('admin.qlybluantintuc.comment.unapprove');
            Route::delete('/comment/{comment}', [QlyBluanTinTucController::class, 'destroy'])->name('admin.qlybluantintuc.comment.delete');
        });

        // Quản lý bình luận bài viết sự kiện
        Route::prefix('list-comment-event')->group(function () {
            Route::get('/', [QlyBluanSuKienController::class, 'index'])->name('admin.qlybluansukien.list');
            Route::get('/{post}/comments', [QlyBluanSuKienController::class, 'comments'])->name('admin.qlybluansukien.comments');
            Route::post('/comment/{comment}/approve', [QlyBluanSuKienController::class, 'approve'])->name('admin.qlybluansukien.comment.approve');
            Route::post('/comment/{comment}/unapprove', [QlyBluanSuKienController::class, 'unapprove'])->name('admin.qlybluansukien.comment.unapprove');
            Route::delete('/comment/{comment}', [QlyBluanSuKienController::class, 'destroy'])->name('admin.qlybluansukien.comment.delete');
        });

        // Quản lý bình luận bài viết blog
        Route::prefix('list-comment-blog')->group(function () {
            Route::get('/', [QlyBluanBlogController::class, 'index'])->name('admin.qlybluanblog.list');
            Route::get('/{post}/comments', [QlyBluanBlogController::class, 'comments'])->name('admin.qlybluanblog.comments');
            Route::post('/comment/{comment}/approve', [QlyBluanBlogController::class, 'approve'])->name('admin.qlybluanblog.comment.approve');
            Route::post('/comment/{comment}/unapprove', [QlyBluanBlogController::class, 'unapprove'])->name('admin.qlybluanblog.comment.unapprove');
            Route::delete('/comment/{comment}', [QlyBluanBlogController::class, 'destroy'])->name('admin.qlybluanblog.comment.delete');
        });

        // Quản lý bình luận bài viết ưu đãi
        Route::prefix('list-comment-uudai')->group(function () {
            Route::get('/', [QlyBluanUuDaiController::class, 'index'])->name('admin.qlybluanuudai.list');
            Route::get('/{post}/comments', [QlyBluanUuDaiController::class, 'comments'])->name('admin.qlybluanuudai.comments');
            Route::post('/comment/{comment}/approve', [QlyBluanUuDaiController::class, 'approve'])->name('admin.qlybluanuudai.comment.approve');
            Route::post('/comment/{comment}/unapprove', [QlyBluanUuDaiController::class, 'unapprove'])->name('admin.qlybluanuudai.comment.unapprove');
            Route::delete('/comment/{comment}', [QlyBluanUuDaiController::class, 'destroy'])->name('admin.qlybluanuudai.comment.delete');
        });

        // Quản lý video
        Route::prefix('list-video')->group(function () {
            Route::get('/', [VideoController::class, 'index'])->name('admin.video.list');
            Route::get('/create', [VideoController::class, 'create'])->name('admin.video.create');
            Route::post('/store', [VideoController::class, 'store'])->name('admin.video.store');
            Route::get('/{id}/edit', [VideoController::class, 'edit'])->name('admin.video.edit');
            Route::put('/{id}', [VideoController::class, 'update'])->name('admin.video.update');
            Route::delete('/delete-multiple', [VideoController::class, 'deleteMultiple'])->name('admin.video.deleteMultiple');
            Route::delete('/{id}', [VideoController::class, 'destroy'])->name('admin.video.destroy');
            Route::put('/{id}/toggle-active', [VideoController::class, 'thaydoitrangthai'])->name('admin.video.toggleActive');
            Route::put('/{id}/update-stt', [VideoController::class, 'updateStt'])->name('admin.video.updateStt');
        });

        Route::prefix('list-san-pham')->group(function () {
            Route::get('/', [SanPhamController::class, 'index'])->name('admin.sanpham.list');
            Route::get('/create', [SanPhamController::class, 'create'])->name('admin.sanpham.create');
            Route::post('/store', [SanPhamController::class, 'store'])->name('admin.sanpham.store');
            Route::get('/{id}/edit', [SanPhamController::class, 'edit'])->name('admin.sanpham.edit');
            Route::put('/{id}', [SanPhamController::class, 'update'])->name('admin.sanpham.update');
            Route::delete('/delete-multiple', [SanPhamController::class, 'deleteMultiple'])->name('admin.sanpham.deleteMultiple');
            Route::delete('/{id}', [SanPhamController::class, 'destroy'])->name('admin.sanpham.destroy');
            Route::put('/{id}/toggle-active', [SanPhamController::class, 'thaydoitrangthai'])->name('admin.sanpham.toggleActive');
            Route::put('/{id}/update-stt', [SanPhamController::class, 'updateStt'])->name('admin.sanpham.updateStt');
            Route::put('/{id}/toggle-is_featured', [SanPhamController::class, 'thaydoinoibat'])->name('admin.sanpham.togglefeatured');
        });

         Route::prefix('list-speech-to-text')->group(function () {
            Route::get('/', [SpeechToTextController::class, 'index'])->name('admin.speechtotext.list');
            Route::get('/create', [SpeechToTextController::class, 'create'])->name('admin.speechtotext.create');
            Route::post('/store', [SpeechToTextController::class, 'store'])->name('admin.speechtotext.store');
            Route::post('/transcribe', [SpeechToTextController::class, 'transcribe'])->name('admin.speechtotext.transcribe');
            Route::get('/{id}/edit', [SpeechToTextController::class, 'edit'])->name('admin.speechtotext.edit');
            Route::put('/{id}', [SpeechToTextController::class, 'update'])->name('admin.speechtotext.update');
            Route::delete('/delete-multiple', [SpeechToTextController::class, 'deleteMultiple'])->name('admin.speechtotext.deleteMultiple');
            Route::delete('/{id}', [SpeechToTextController::class, 'destroy'])->name('admin.speechtotext.destroy');
            Route::put('/{id}/toggle-active', [SpeechToTextController::class, 'thaydoitrangthai'])->name('admin.speechtotext.toggleActive');
            Route::put('/{id}/update-stt', [SpeechToTextController::class, 'updateStt'])->name('admin.speechtotext.updateStt');
            Route::put('/{id}/toggle-is_featured', [SpeechToTextController::class, 'thaydoinoibat'])->name('admin.speechtotext.togglefeatured');
        });

    });


    Route::get('/icons-json', function () {
        $path = public_path('icons.json');
        if (!file_exists($path)) {
            return response()->json([]);
        }
        $icons = json_decode(file_get_contents($path), true);
        return response()->json($icons);
    });

    Route::post('/upload-image', [ImageUploadController::class, 'upload'])->name('upload.image');

    // Log hệ thống
    Route::middleware(['auth'])->prefix('admin')->group(function () {
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('admin.activity-logs.list');
        Route::get('/logs/{id}', [ActivityLogController::class, 'show'])->name('admin.logs.show');
    });

    Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web']], function () {
        Lfm::routes();
    });


// routes/api.php
Route::get('/list-menu-frontend', [MenuFrontendController::class, 'apiIndex']);
// Lấy danh sách sản phẩm theo category_id
Route::get('/products/category/{category_id}', [SanPhamController::class, 'getByCategory']);
Route::get('/categories', [CategoryController::class, 'apiIndex']);
Route::get('/products/{id}', [SanPhamController::class, 'show']);

Route::get('/check-gcloud-key', function () {
    return env('GOOGLE_APPLICATION_CREDENTIALS');
});
