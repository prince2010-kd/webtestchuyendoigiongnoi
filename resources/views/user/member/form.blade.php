@extends('layouts.app')

@section('title', 'Create New User')

@section('content')
<div class="container-xxl my-4 vh-100">
    <div>Thêm mới</div>
    <form class="my-4" id="create-form">
        <div class="row mb-3">
            <label for="username" class="form-label col-sm-2">Tài khoản</label>
            <div class="col-sm-10">
                <input
                    type="text"
                    class="form-control"
                    id="username"
                />
            </div>
        </div>

        <div class="row mb-3">
            <div class="offset-sm-2 col-sm-10 d-flex align-items-center gap-2">
                <input 
                    type="checkbox"
                    class="form-check-input"
                    id="isAdmin"
                />
                <label class="" for="isAdmin">Là quản trị</label>
            </div>
        </div>

        <div class="row mb-3">
            <label for="fullname" class="col-sm-2">Họ tên</label>
            <div class="col-sm-10">
                <input 
                    type="text"
                    class="form-control"
                    id="fullname"
                />
            </div>
        </div>

        <div class="row mb-3">
            <label for="email" class="col-sm-2">Email</label>
            <div class="col-sm-10">
                <input 
                    type="text"
                    class="form-control"
                    id="email"
                />
            </div>
        </div>

        <div class="row mb-3">
            <label for="fullname" class="col-sm-2">Nhóm quyền</label>
            <div class="col-sm-10">
                <select class="form-control" id="permis">
                    <option>Chọn nhóm quyền</option>
                    <option value="1">Supper Admin</option>
                    <option value="2">Nhóm dữ liệu kì thi</option>
                    <option value="3">Nhóm nội dung</option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <label for="password" class="col-sm-2">Mật khẩu mới</label>
            <div class="col-sm-10">
                <input 
                    type="password"
                    class="form-control"
                    id="password"
                />
            </div>
        </div>
        
        <div class="row mb-3">
            <label for="rewritepass" class="col-sm-2">Nhập lại mật khẩu</label>
            <div class="col-sm-10">
                <input 
                    type="password"
                    class="form-control"
                    id="rewritepass"
                />
            </div>
        </div>
        <div class="d-flex justify-content-center align-items-center">
            <button type="submit" class="btn btn-primary col-sm-2 text-white">Tạo mới</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
    <script>
        const info = document.getElementById;
        document.getElementById('create-form').addEventListener('submit', async function(e){
            e.preventDefault();

            const username = document.getElementById('username').value;
            const isAdmin = document.getElementById('isAdmin').value;
            const fullname = document.getElementById('fullname').value;
            const email = document.getElementById('email').value;
            const permis = document.getElementById('permis').value;
            const password = document.getElementById('password').value;
            const rewritepass = document.getElementById('rewritepass').value;

            const data = {
                username: username,
                isAdmin: isAdmin,
                fullname: fullname,
                email: email,
                permis: permis,
                password: password,
                rewritepass: rewritepass,
            };

            try{
                const res = await fetch("/api/users", {
                    method: "POST",
                    headers: {
                        'Content-Type': "application/json",
                        "Accept": "application/json",
                    },
                    body: JSON.stringify(data)
                });

                const result = await res.json();
                if(res.ok)
                {
                    console.log("succ");
                }else
                {
                    console.log("err");
                }
            }catch (error){
                console.log(error);
            }

 
        })
    </script>
@endsection