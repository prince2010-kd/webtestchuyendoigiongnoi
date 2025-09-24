@extends('layouts.app')

@section('title', 'Login Page')

@section('content')
    <div class="container-xxl d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow p-4" style="max-width: 400px; width: 100%;">
        <h3 class="text-center mb-4">Đăng nhập hệ thống</h3>
        <form id="login-form">
            <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input
                type="email"
                class="form-control"
                id="email"
                placeholder="Tài khoản"
               
                required
            />
            </div>

            <div class="mb-3">
            <label for="password" class="form-label">Mật khẩu</label>
            <input
                type="password"
                class="form-control"
                id="password"
                placeholder="Mật khẩu"
                
                required
            />
            </div>

            <button type="submit" class="btn btn-primary w-100 text-white">Đăng nhập</button>
            <div id="result" class="text-danger mt-2 text-center"></div>
        </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#login-form').submit(function(e){
                e.preventDefault();

                $.ajax({
                    url:'/admin/login',
                    method: 'POST',
                    data: {
                        email: $("#email").val(),
                        password: $("#password").val(),
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    xhrFields: {
                        withCredentials: true
                    },

                    success: function(response){
                        window.location.href = '/admin/dashboard';
                    },

                    error: function(xhr){
    console.log('Status:', xhr.status);
    console.log('Response:', xhr.responseText);
    if(xhr.status == 401){
        $('#result').text(xhr.responseJSON.error)
    }else{
        $('#result').text('Có lỗi xảy ra');
    }
}


                })
            })
        });

        async function fetchAdminInfo()
        {
            const token = localStorage.getItem('jwt_token');
            if(!token) 
            {
                info.textContent = 'Guest';
                return;   
            }
            try{
                const res = await fetch('/api/me', {
                    headers:{
                        'Authorization': 'Bearer ' + token,
                        'Accept':'application/json'
                    }
                });
                const data = await res.json();
                if(!res.ok)
                {
                    info.textContent = data.error       ;
                    return;
                }

                info.textContent = JSON.stringify(data, null, 2);
            }
            catch(error)
            {
                info.textContent = 'Server went wrong';
            }
        }
    </script>
@endsection