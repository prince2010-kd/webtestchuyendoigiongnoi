<table class="table table-bordered" id="post-data">
    <thead>
        <tr>
            <th>ID</th>
            <th style="width: 180px">Người thực hiện</th>
            <th>Bảng</th>
            <th style="width: 130px">Hành động</th>
            <th>Mô tả</th>
            <th style="width: 180px">Thời gian</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($logs as $log)
            <tr>
                <td>{{ $log->id }}</td>
                <td>{{ $log->causer ? $log->causer->name : 'Không xác định' }}</td>

                @php 
                    $table = 'Không xác định';
                    if ($log->subject_type && class_exists($log->subject_type)) {
                        $model = new $log->subject_type;
                        if (method_exists($model, 'getTable')) {
                            $table = $model->getTable();
                        }
                    }
                @endphp

                <td>{{ $table }}</td>

                @php
                    $parts = explode(':', $log->description, 2);
                    $action = trim($parts[0] ?? '');
                    $desc = trim($parts[1] ?? '');
                @endphp
                <td>{{ $action }}</td>
                <td>{{ $desc }}</td>
                <td>{{ $log->created_at->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>