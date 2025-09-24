<?php

namespace App\Http\Controllers\backend\user;

use App\Enums\FormMode;
use App\Http\Controllers\Controller;
use App\Models\NhomQuyen;
use App\Models\Permission;
use App\Models\Quyen;
use App\Models\RefreshToken;
use App\Models\User;
use Elastic\Elasticsearch\Endpoints\Cat;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
class UserController extends Controller
{
    public function showLogin()
    {
        return view('user.login');
    }

    public function login(Request $request)
{
    $credentials = $request->only('email', 'password');
Log::info('Login attempt with:', $credentials);
    // Lấy user từ DB
    $user = User::where('email', $credentials['email'])->first();
 Log::info('User found: ' . json_encode($user));
    // Nếu không tìm thấy hoặc tài khoản bị vô hiệu
    if (!$user || !$user->active) {
        return response()->json(['error' => 'Tài khoản không tồn tại'], 401);
    }

    // Xác thực thông tin đăng nhập và tạo token
    if (!$token = JWTAuth::attempt($credentials)) {
        return response()->json(['error' => 'Sai tài khoản hoặc mật khẩu'], 401);
    }

    // Tạo cookie chứa token
    $accessCookie = cookie('token', $token, 1440, '/', null, false, true);

    // Tạo refresh token
    $plainRefreshToken = Str::random(64);
    RefreshToken::updateOrCreate(
        ['user_id' => $user->id],
        [
            'token' => hash('sha256', $plainRefreshToken),
            'expires_at' => now()->addDays(14)
        ]
    );
    $refreshCookie = cookie('refresh_token', $plainRefreshToken, 20160, '/', null, false, true);

    // Cache quyền
    $nhomQuyen = optional(NhomQuyen::find($user->group_id))->id;
    if (!$nhomQuyen) {
        return response()->json(['error' => 'Không tìm thấy nhóm quyền của người dùng'], 404);
    }
    cacheQuyenGroup($nhomQuyen);

    return response()->json(['message' => 'Đăng nhập thành công'])
        ->cookie($accessCookie)
        ->cookie($refreshCookie);
}


    public function refresh(Request $request)
    {
        Log::info("start refresh token");
        // Lấy refresh token từ cookie của client gửi lên
        $plainToken = $request->cookie('refresh_token');

        if (!$plainToken) {
            return response()->json(['error' => 'No refresh token'], 401);
        }
        //Băm (hash) token lấy được
        $hashed = hash('sha256', $plainToken);
        $record = RefreshToken::when('token', $hashed)
            ->where('expires_at', '>', now())
            ->first();
        if (!$record) {
            return response()->json(['error' => 'Invalid refresh token'], 401);
        }
        //Tìm user từ RefreshToken
        $user = User::find($record->user_id);
        //Từ user, tạo access token mới
        $newToken = JWTAuth::fromUser($user);
        $newAccessCookie = cookie('token', $newToken, 15, '/', null, false, true);
        //trả về cookie 
        return response()->json(['message' => 'Token refreshed'])->cookie($newAccessCookie);
    }
    public function me()
    {
        return response()->json(Auth::user());
    }

   public function index(Request $request)
{
    $query = User::query();

    // Tìm kiếm theo tên hoặc email
    if ($request->filled('keyword')) {
        $keyword = $request->input('keyword');
        $query->where(function ($q) use ($keyword) {
            $q->where('name', 'like', '%' . $keyword . '%')
              ->orWhere('email', 'like', '%' . $keyword . '%');
        });
    }

    // Sắp xếp theo cột được chọn hoặc mặc định là theo 'stt'
    if ($request->filled('sortBy')) {
            $sort = $request->input('sortBy') == 'desc' ? 'desc' : 'asc';
            $query->orderBy('name', $sort);
        } else {
            $query->orderBy('email', 'asc');
        }

    // Số bản ghi mỗi trang
    $pageSize = $request->input('page_size', 10);
    $users = $query->paginate($pageSize)->appends($request->all());


    // Nếu là request AJAX thì render table và phân trang riêng
    if ($request->ajax()) {
        $table = view('admin.user.partials._table', compact('users', ))->render();
        $pagination = view('admin.component.pagination', ['posts' => $users])->render();

        return response()->json([
            'table' => $table,
            'pagination' => $pagination,
            'status' => true
        ]);
    }

    return view('admin.user.list', compact('users'));
}


    public function create()
    {
        $nhomQuyen = NhomQuyen::all();
        $mode = FormMode::FORM_CREATE;
        return view('admin.user.form', compact('nhomQuyen', 'mode'));
    }

    public function store(Request $request)
    {
        $newUser = new User();
        $userData = $request->only($newUser->getFillable());

        // Gán group_id mặc định nếu không có permis
        if ($request->filled('permis')) {
            $userData['group_id'] = (int) $request->input('permis');
        } else {
            $userData['group_id'] = 1; // mặc định
        }

        // Mã hóa mật khẩu nếu có
        if (isset($userData['password'])) {
            $userData['password'] = bcrypt($userData['password']);
        }

        User::create($userData);

        return response()->json([
            'data' => $userData,
            'message' => 'Tạo mới thành công'
        ]);
    }


    public function edit($id)
    {
        $user = User::find($id);
        $nhomQuyen = NhomQuyen::all();
        $mode = FormMode::FORM_EDIT;
        return view('admin.user.form', compact('user', 'nhomQuyen', 'mode'));
    }


    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->only($user->getFillable());

        // Gán lại group_id từ permis (nếu có)
        if ($request->filled('permis')) {
            $data['group_id'] = (int) $request->input('permis');
        }

        // Nếu có cập nhật mật khẩu mới
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']); // Không thay đổi mật khẩu nếu không nhập
        }

        $user->update($data);

        return response()->json([
            'message' => 'Cập nhật thành công',
            'data' => $user
        ]);
    }


    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json(['success' => true, 'message' => 'Deleted user successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'User does not exist.'], 404);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong'], 500);
        }
    }

    public function updateStt(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->stt = $request->input('stt');
            $user->save();
            return response()->json(['success' => true, 'message' => 'Update stt successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'User does not exist.'], 404);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong'], 500);
        }
    }

    public function toggleStatus(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->active = $request->input('active');
            $user->save();
            return response()->json(['success' => true, 'message' => 'Cập nhật trạng thái thành công']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Người dùng không tồn tại'], 404);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Cập nhật thất bại'], 500);
        }
    }

    public function logout()
    {
        auth()->logout();

        return redirect('/backend');
    }

    public function editOwnAccount()
    {
        $user = auth()->user();
        $mode = FormMode::FORM_EDIT_SELF;
        $nhomQuyen = [];

        return view('admin.user.form', compact('user', 'nhomQuyen', 'mode'));

    }

}
