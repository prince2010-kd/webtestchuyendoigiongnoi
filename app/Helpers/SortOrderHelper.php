<?php
namespace App\Helpers;
use Illuminate\Database\Eloquent\Model;

class SortOrderHelper
{
    public static function updateStt(string $modelClass, int $id, int $newStt, array $conditions = []): array
    {
        $record = $modelClass::findOrFail($id);

        if ($record->stt == $newStt) {
            return ['status' => false, 'message' => 'STT không thay đổi'];
        }

        $query = $modelClass::where('id', '!=', $id);

        foreach ($conditions as $field => $value) {
            $query->where($field, $value);
        }

        $siblings = $query->orderBy('stt')->get()->values();
        $siblings->splice($newStt - 1, 0, [$record]);

        foreach ($siblings as $index => $item) {
            $item->stt = $index + 1;
            $item->save();
        }

        return ['status' => true, 'message' => 'Cập nhật STT thành công'];
    }
}